<?php
declare(strict_types = 1);

namespace Aoe\AoeDbSequenzer\Xclass;

/*
 * This file xclasses and extends a part of the TYPO3 CMS project.
 */

use Aoe\AoeDbSequenzer\Sequenzer;
use Aoe\AoeDbSequenzer\Service\Typo3Service;
use TYPO3\CMS\Core\Database\Connection as CoreConnection;
use TYPO3\CMS\Core\Database\Query\BulkInsertQuery;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @package Aoe\AoeDbSequenzer\Xclass
 */
class Connection extends CoreConnection
{
    /**
     * @var Typo3Service
     */
    private $typo3Service;

    /**
     * Inserts a table row with specified data.
     *
     * All SQL identifiers are expected to be unquoted and will be quoted when building the query.
     * Table expression and columns are not escaped and are not safe for user-input.
     *
     * @param string $tableName The name of the table to insert data into.
     * @param array  $data      An associative array containing column-value pairs.
     * @param array  $types     Types of the inserted data.
     *
     * @return int The number of affected rows.
     */
    public function insert($tableName, array $data, array $types = []): int
    {
        return parent::insert(
            $tableName,
            $this->getTypo3Service()->modifyInsertFields($tableName, $data),
            $types
        );
    }

    /**
     * Bulk inserts table rows with specified data.
     *
     * All SQL identifiers are expected to be unquoted and will be quoted when building the query.
     * Table expression and columns are not escaped and are not safe for user-input.
     *
     * @param string $tableName The name of the table to insert data into.
     * @param array  $data      An array containing associative arrays of column-value pairs.
     * @param array  $columns   An array containing associative arrays of column-value pairs.
     * @param array  $types     Types of the inserted data.
     *
     * @return int The number of affected rows.
     */
    public function bulkInsert(string $tableName, array $data, array $columns = [], array $types = []): int
    {
        $query = GeneralUtility::makeInstance(BulkInsertQuery::class, $this, $tableName, $columns);
        foreach ($data as $values) {
            $query->addValues($this->getTypo3Service()->modifyInsertFields($tableName, $values), $types);
        }

        return $query->execute();
    }

    /**
     * Executes an SQL UPDATE statement on a table.
     *
     * All SQL identifiers are expected to be unquoted and will be quoted when building the query.
     * Table expression and columns are not escaped and are not safe for user-input.
     *
     * @param string $tableName  The name of the table to update.
     * @param array  $data       An associative array containing column-value pairs.
     * @param array  $identifier The update criteria. An associative array containing column-value pairs.
     * @param array  $types      Types of the merged $data and $identifier arrays in that order.
     *
     * @return int The number of affected rows.
     */
    public function update($tableName, array $data, array $identifier, array $types = []): int
    {
        if (isset($data['uid']) && $this->getTypo3Service()->needsSequenzer($tableName)) {
            throw new \InvalidArgumentException('no uid allowed in update statement!', 1564122222);
        }

        return parent::update(
            $tableName,
            $data,
            $identifier,
            $types
        );
    }


    /**
     * create instance of Typo3Service by lazy-loading
     *
     * Why we do this?
     * Because some unittests backup the variable $GLOBALS (and so, also the variable $GLOBALS['TYPO3_DB']), which means, that this
     * object/class will be serialized/unserialized, so the instance of Typo3Service will be null after unserialization!
     *
     * @return Typo3Service
     */
    protected function getTypo3Service()
    {
        if (false === isset($this->typo3Service)) {
            $this->typo3Service = new Typo3Service(new Sequenzer());
        }

        return $this->typo3Service;
    }
}
