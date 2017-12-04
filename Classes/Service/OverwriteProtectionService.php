<?php
namespace Aoe\AoeDbSequenzer\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 AOE GmbH (dev@aoe.com)
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Aoe\AoeDbSequenzer\Domain\Model\OverwriteProtection;
use Aoe\AoeDbSequenzer\Domain\Repository\OverwriteProtectionRepository;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 * @package Aoe\AoeDbSequenzer
 */
class OverwriteProtectionService
{
    /**
     * @var string
     */
    const OVERWRITE_PROTECTION_TABLE = 'tx_aoedbsequenzer_domain_model_overwriteprotection';

    /**
     * @var string
     */
    const OVERWRITE_PROTECTION_TILL = 'tx_aoe_dbsquenzer_protectoverwrite_till';

    /**
     * @var string
     */
    const OVERWRITE_PROTECTION_MODE = 'tx_aoe_dbsquenzer_protectoverwrite_mode';

    /**
     * array of configured tables that should call the sequenzer
     *
     * @var array
     */
    private $supportedTables;

    /**
     * @var OverwriteProtectionRepository
     */
    private $overwriteProtectionRepository;

    /**
     * @var PersistenceManager
     */
    private $persistenceManager;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct()
    {
        $extConf = unserialize($GLOBALS ['TYPO3_CONF_VARS'] ['EXT'] ['extConf'] ['aoe_dbsequenzer']);
        $explodedValues = explode(',', $extConf ['tables']);
        $this->supportedTables = array_map('trim', $explodedValues);

        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->overwriteProtectionRepository = $this->objectManager->get(OverwriteProtectionRepository::class);
        $this->persistenceManager = $this->objectManager->get(PersistenceManager::class);
    }

    /**
     * Hook for deletes in Typo3 Backend. It also delete all overwrite protection
     * @param string $command
     * @param string $table
     * @param integer $id
     */
    public function processCmdmap_postProcess($command, $table, $id)
    {
        if (false === $this->needsOverWriteProtection($table)) {
            return;
        }
        if ($command !== 'delete') {
            return;
        }
        $this->removeOverwriteProtection($id, $table);
    }

    /**
     * Hook for updates in Typo3 backend
     * @param array $incomingFieldArray
     * @param string $table
     * @param integer $id
     * @param DataHandler $tcemain
     */
    public function processDatamap_preProcessFieldArray(&$incomingFieldArray, $table, $id, DataHandler &$tcemain)
    {
        if (false === $this->needsOverWriteProtection($table)) {
            return;
        }

        // check, if overwrite-protection-fields are set:
        // If they are NOT set, it means, that any other extension maybe called the process_datamap!
        if (false === array_key_exists(self::OVERWRITE_PROTECTION_TILL, $incomingFieldArray) ||
            false === array_key_exists(self::OVERWRITE_PROTECTION_MODE, $incomingFieldArray)
        ) {
            return;
        }

        if (false === $this->hasOverWriteProtection($incomingFieldArray)) {
            $this->removeOverwriteProtection($id, $table);
        } else {
            $protectionTime = $incomingFieldArray [self::OVERWRITE_PROTECTION_TILL];
            $mode = $incomingFieldArray [self::OVERWRITE_PROTECTION_MODE];

            $protectionTime = $this->convertClientTimestampToUTC($protectionTime, $table, $tcemain);

            $queryResult = $this->overwriteProtectionRepository->findByProtectedUidAndTableName($id, $table);
            if ($queryResult->count() === 0) {
                /* @var $overwriteProtection OverwriteProtection */
                $overwriteProtection = $this->objectManager->get(OverwriteProtection::class);
                $overwriteProtection->setProtectedMode($mode);
                $overwriteProtection->setPid($tcemain->getPID($table, $id));
                $overwriteProtection->setProtectedTablename($table);
                $overwriteProtection->setProtectedUid($id);
                $overwriteProtection->setProtectedTime($protectionTime);
                $this->overwriteProtectionRepository->add($overwriteProtection);
            } else {
                /* @var $overwriteProtection OverwriteProtection */
                $overwriteProtection = $queryResult->getFirst();
                $overwriteProtection->setProtectedMode($mode);
                $overwriteProtection->setProtectedTime($protectionTime);
                $this->overwriteProtectionRepository->update($overwriteProtection);
            }
            $this->persistenceManager->persistAll();
        }
        unset ($incomingFieldArray [self::OVERWRITE_PROTECTION_TILL]);
        unset ($incomingFieldArray [self::OVERWRITE_PROTECTION_MODE]);
    }

    /**
     * @param array $fields_values
     * @return boolean
     */
    private function hasOverWriteProtection(array $fields_values)
    {
        if (isset ($fields_values [self::OVERWRITE_PROTECTION_TILL])) {
            $value = trim($fields_values [self::OVERWRITE_PROTECTION_TILL]);
            if (false === empty ($value) && false !== is_numeric($value)) {
                return true;
            }
        }
        return false;
    }

    /**
     * If a table is configured to use the sequenzer
     *
     * @param string $tableName
     * @return boolean
     */
    private function needsOverWriteProtection($tableName)
    {
        if ($tableName !== self::OVERWRITE_PROTECTION_TABLE && in_array($tableName, $this->supportedTables)) {
            return true;
        }
        return false;
    }

    /**
     * remove overwriteProtection
     *
     * @param integer $id
     * @param string $table
     */
    private function removeOverwriteProtection($id, $table)
    {
        $queryResult = $this->overwriteProtectionRepository->findByProtectedUidAndTableName($id, $table);
        $overwriteProtection = $queryResult->getFirst();
        $this->overwriteProtectionRepository->remove($overwriteProtection);
        $this->persistenceManager->persistAll();
    }

    /**
     * @param string $dateTimeValue
     * @param string $table
     * @param DataHandler $dataHandler
     * @return string
     */
    private function convertClientTimestampToUTC($dateTimeValue, $table, DataHandler $dataHandler)
    {
        $evalArray = explode(',', $GLOBALS['TCA'][$table]['columns'][self::OVERWRITE_PROTECTION_TILL]['config']['eval']);

        $evalResult = $dataHandler->checkValue_input_Eval($dateTimeValue, $evalArray, null);

        if (isset($evalResult['value'])) {
            return $evalResult['value'];
        }

        return $dateTimeValue;
    }
}
