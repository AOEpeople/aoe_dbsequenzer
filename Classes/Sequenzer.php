<?php

namespace Aoe\AoeDbSequenzer;

use Doctrine\DBAL\Types\Types;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
 */
class Sequenzer
{
    /**
     * @var string
     */
    public const SEQUENZER_TABLE = 'tx_aoedbsequenzer_sequenz';

    private int $defaultStart = 0;

    private int $defaultOffset = 1;

    /**
     * @var int in seconds
     */
    private int $checkInterval = 120;

    public function setDefaultStart(int $defaultStart): void
    {
        $this->defaultStart = $defaultStart;
    }

    public function setDefaultOffset(int $defaultOffset): void
    {
        $this->defaultOffset = $defaultOffset;
    }

    /**
     * returns next free id in the sequenz of the table
     */
    public function getNextIdForTable(string $table, int $depth = 0): int
    {
        if ($depth > 99) {
            throw new \Exception(
                'The sequenzer cannot return IDs for this table -' . $table . ' Too many recursions - maybe to much load?',
                1512378158
            );
        }

        /** @var Connection $databaseConnection */
        $databaseConnection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable(self::SEQUENZER_TABLE);
        $row = $databaseConnection->select(['*'], self::SEQUENZER_TABLE, ['tablename' => $table])->fetchAssociative();

        if (!isset($row['current'])) {
            $this->initSequenzerForTable($table);
            return $this->getNextIdForTable($table, ++$depth);
        }

        if ($row['timestamp'] + $this->checkInterval < $GLOBALS['EXEC_TIME']) {
            $defaultStartValue = $this->getDefaultStartValue($table);
            $isValueOutdated = ($row['current'] < $defaultStartValue);
            $isOffsetChanged = ($row['offset'] != $this->defaultOffset);
            $isStartChanged = ($this->defaultStart != $row['current'] % $this->defaultOffset);
            if ($isValueOutdated || $isOffsetChanged || $isStartChanged) {
                $row['current'] = $defaultStartValue;
            }
        }

        $new = $row['current'] + $row['offset'];
        $updateTimeStamp = $GLOBALS['EXEC_TIME'];
        $updateResult = $databaseConnection->update(
            self::SEQUENZER_TABLE,
            ['current' => $new, 'timestamp' => $updateTimeStamp],
            ['timestamp' => $row['timestamp'], 'tablename' => $table]
        );

        if ($updateResult > 0) {
            return $new;
        }

        return $this->getNextIdForTable($table, ++$depth);
    }

    /**
     * Gets the default start value for a given table.
     */
    private function getDefaultStartValue(string $table): int
    {
        /** @var Connection $databaseConnection */
        $databaseConnection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable(self::SEQUENZER_TABLE);
        $row = $databaseConnection->select(['uid'], $table, [], [], ['uid' => 'DESC'], 1)->fetchAssociative();

        if (!isset($row['uid'])) {
            return $this->defaultStart + $this->defaultOffset;
        }

        $currentMax = $row['uid'] + 1;
        $start = $this->defaultStart + ($this->defaultOffset * ceil($currentMax / $this->defaultOffset));

        return (int) $start;
    }

    /**
     * if no scheduler entry for the table yet exists, this method initialises the sequenzer to fit offest and start and current max value
     * in the table
     */
    private function initSequenzerForTable(string $table): void
    {
        $start = $this->getDefaultStartValue($table);

        /** @var Connection $databaseConnection */
        $databaseConnection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable(self::SEQUENZER_TABLE);
        $databaseConnection->insert(
            self::SEQUENZER_TABLE,
            ['tablename' => $table, 'current' => $start, 'offset' => $this->defaultOffset, 'timestamp' => $GLOBALS['EXEC_TIME']],
            [Types::STRING, Types::INTEGER, Types::INTEGER, Types::STRING]
        );
    }
}
