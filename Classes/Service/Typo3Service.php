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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @package Aoe\AoeDbSequenzer
 */
class Typo3Service
{
    /**
     * @var Sequenzer
     */
    private $sequenzer;

    /**
     * @var array
     */
    private $conf;

    /**
     * array of configured tables that should call the sequenzer
     *
     * @var array
     */
    private $supportedTables;

    /**
     * @param Sequenzer $sequenzer
     */
    public function __construct(Sequenzer $sequenzer)
    {
        $this->conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['aoe_dbsequenzer']);

        $this->sequenzer = $sequenzer;
        $this->sequenzer->setDefaultOffset(intval($this->conf['offset']));
        $this->sequenzer->setDefaultStart(intval($this->conf['system']));

        $explodedValues = explode(',', $this->conf['tables']);
        $this->supportedTables = array_map('trim', $explodedValues);
    }

    /**
     * sets the db link
     *
     * @param \mysqli|NULL $link
     */
    public function setDbLink($link)
    {
        $this->sequenzer->setDbLink($link);
    }

    /**
     * Modify a TYPO3 insert array (key -> value) , and adds the uid that should be forced during INSERT
     *
     * @param string $tableName
     * @param array $fields_values
     * @return array
     */
    public function modifyInsertFields($tableName, array $fields_values)
    {
        if (false === $this->needsSequenzer($tableName)) {
            return $fields_values;
        }

        if (isset($fields_values['uid'])) {
            $e = new \Exception('', 1512378232);
            GeneralUtility::devLog(
                'UID ' . $fields_values['uid'] . ' is already set for table "' . $tableName . '"',
                'aoe_dbsequenzer',
                2,
                $e->getTraceAsString()
            );
        } else {
            $fields_values['uid'] = $this->sequenzer->getNextIdForTable($tableName);
        }

        return $fields_values;
    }

    /**
     * @param string $tableName
     * @param string $fieldName
     * @param mixed  $fieldValue
     *
     * @return mixed
     */
    public function modifyField(string $tableName, string $fieldName, $fieldValue)
    {
        $data = $this->modifyInsertFields($tableName, [$fieldName => $fieldValue]);
        return $data[$fieldName];
    }

    /**
     * If a table is configured to use the sequenzer
     *
     * @param string $tableName
     * @return boolean
     */
    public function needsSequenzer($tableName)
    {
        if (in_array($tableName, $this->supportedTables)) {
            return true;
        }
        return false;
    }
}
