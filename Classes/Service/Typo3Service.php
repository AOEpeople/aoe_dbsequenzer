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

use Aoe\AoeDbSequenzer\Sequenzer;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @package Aoe\AoeDbSequenzer
 */
class Typo3Service implements SingletonInterface
{
    /**
     * @var Sequenzer
     */
    private $sequenzer;

    /**
     * @var array
     */
    private $conf = [];

    /**
     * @var Logger
     */
    private $logger;

    /**
     * array of configured tables that should call the sequenzer
     *
     * @var array
     */
    private $supportedTables = [];

    public function __construct(Sequenzer $sequenzer)
    {
        $this->conf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('aoe_dbsequenzer');

        $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);

        $this->sequenzer = $sequenzer;
        $this->sequenzer->setDefaultOffset((int) $this->conf['offset']);
        $this->sequenzer->setDefaultStart((int) $this->conf['system']);

        $explodedValues = explode(',', $this->conf['tables']);
        $this->supportedTables = array_map('trim', $explodedValues);
    }

    /**
     * Modify a TYPO3 insert array (key -> value) , and adds the uid that should be forced during INSERT
     *
     * @param string $tableName
     */
    public function modifyInsertFields($tableName, array $fields_values): array
    {
        if (!$this->needsSequenzer($tableName)) {
            return $fields_values;
        }

        // How to test this when no exception is thrown ?
        if (isset($fields_values['uid'])) {
            $e = new \Exception('', 1512378232);
            $this->logger->debug(
                'UID ' . $fields_values['uid'] . ' is already set for table "' . $tableName . '"',
                [
                    'aoe_dbsequenzer',
                    2,
                    $e->getTraceAsString(),
                ]
            );
        } else {
            $fields_values['uid'] = $this->sequenzer->getNextIdForTable($tableName);
        }

        return $fields_values;
    }

    /**
     * If a table is configured to use the sequenzer
     *
     * @param string $tableName
     * @return boolean
     */
    public function needsSequenzer($tableName)
    {
        return in_array($tableName, $this->supportedTables);
    }
}
