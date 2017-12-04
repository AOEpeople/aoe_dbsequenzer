<?php
namespace Aoe\AoeDbSequenzer;

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

/**
 * Sequenzer is used to generate system wide independent IDs
 * @package Aoe\AoeDbSequenzer
 */
class Sequenzer
{
    /**
     * @var string
     */
    const SEQUENZER_TABLE = 'tx_aoedbsequenzer_sequenz';

    /**
     * @var \mysqli
     */
    private $dbLink;

    /**
     * @var integer
     */
    private $defaultStart = 0;

    /**
     * @var integer
     */
    private $defaultOffset = 1;

    /**
     * @var integer
     */
    private $checkInterval = 120;

    /**
     * @param integer $defaultStart to set
     */
    public function setDefaultStart($defaultStart)
    {
        $this->defaultStart = $defaultStart;
    }

    /**
     * @param integer $defaultOffset to set
     */
    public function setDefaultOffset($defaultOffset)
    {
        $this->defaultOffset = $defaultOffset;
    }

    /**
     * sets mysql dblink with DB connection
     * @param null $dbLink
     */
    public function setDbLink($dbLink = null)
    {
        if (is_null($dbLink)) {
            $this->dbLink = $GLOBALS['TYPO3_DB']->getDatabaseHandle();
        } else {
            $this->dbLink = $dbLink;
        }
    }

    /**
     * returns next free id in the sequenz of the table
     * @param $table
     * @param int $depth
     * @return int
     * @throws \Exception
     */
    public function getNextIdForTable($table, $depth = 0)
    {
        if ($depth > 99) {
            throw new \Exception(
                'The sequenzer cannot return IDs for this table -' . $table . ' Too many recursions - maybe to much load?',
                1512378158
            );
        }

        $result = $this->query('SELECT * FROM ' . self::SEQUENZER_TABLE . ' WHERE tablename=\'' . $this->escapeString($table) . '\'');
        $row = mysqli_fetch_assoc($result);

        if (!isset ($row ['current'])) {
            $this->initSequenzerForTable($table);
            return $this->getNextIdForTable($table, ++$depth);
            //throw new Exception('The sequenzer cannot return IDs for this table -'.$table.'- its not configured!');
        } elseif ($row ['timestamp'] + $this->checkInterval < $GLOBALS ['EXEC_TIME']) {
            $defaultStartValue = $this->getDefaultStartValue($table);
            $isValueOutdated = ($row ['current'] < $defaultStartValue);
            $isOffsetChanged = ($row ['offset'] != $this->defaultOffset);
            $isStartChanged = ($row ['current'] % $this->defaultOffset != $this->defaultStart);
            if ($isValueOutdated || $isOffsetChanged || $isStartChanged) {
                $row ['current'] = $defaultStartValue;
            }
        }

        $new = $row ['current'] + $row ['offset'];
        $updateTimeStamp = $GLOBALS ['EXEC_TIME'];
        $res2 = $this->query('UPDATE ' . self::SEQUENZER_TABLE . ' SET current=' . $new . ', timestamp=' . $updateTimeStamp . ' WHERE timestamp=' . $row ['timestamp'] . ' AND tablename=\'' . $this->escapeString($table) . '\'');
        if ($res2 && mysqli_affected_rows($this->dbLink) > 0) {
            return $new;
        } else {
            return $this->getNextIdForTable($table, ++$depth);
        }
    }

    /**
     * Gets the default start value for a given table.
     * @param $table
     * @return int
     * @throws \Exception
     */
    private function getDefaultStartValue($table)
    {
        $result = $this->query('SELECT max(uid) as max FROM ' . $table);
        $row = mysqli_fetch_assoc($result);
        $currentMax = $row ['max'] + 1;
        $start = $this->defaultStart + ($this->defaultOffset * ceil($currentMax / $this->defaultOffset));

        return $start;
    }

    /**
     * if no sehduler entry for the table yet exists, this method initialises the sequenzer to fit offest and start and current max value in the table
     *
     * @param string $table
     */
    private function initSequenzerForTable($table)
    {
        $start = $this->getDefaultStartValue($table);
        $insert = 'INSERT INTO ' . self::SEQUENZER_TABLE . ' ( tablename, current, offset, timestamp ) VALUES ( \'' . $this->escapeString($table) . '\', ' . $start . ',' . intval($this->defaultOffset) . ',' . $GLOBALS ['EXEC_TIME'] . ' )';
        $this->query($insert);
    }

    /**
     * @param $string
     * @return string
     */
    private function escapeString($string)
    {
        return mysqli_escape_string($this->dbLink, $string);
    }

    /**
     * @param $sql
     * @return bool|\mysqli_result
     * @throws \Exception
     */
    private function query($sql)
    {
        $result = mysqli_query($this->dbLink, $sql);
        if (mysqli_error($this->dbLink)) {
            throw new \Exception(
                mysqli_error($this->dbLink), mysqli_errno($this->dbLink),
                1512378208
            );
        }
        return $result;
    }

}
