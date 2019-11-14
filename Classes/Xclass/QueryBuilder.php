<?php
declare(strict_types = 1);

namespace Aoe\AoeDbSequenzer\Xclass;

/*
 * This file xclasses and extends a part of the TYPO3 CMS project.
 */

use Aoe\AoeDbSequenzer\Sequenzer;
use Aoe\AoeDbSequenzer\Service\Typo3Service;
use TYPO3\CMS\Core\Database\Query\QueryBuilder as CoreQueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @package Aoe\AoeDbSequenzer\Xclass
 */
class QueryBuilder extends CoreQueryBuilder
{
    /**
     * @var Typo3Service
     */
    private $typo3Service;

    /**
     * Sets a new value for a column in a bulk update query.
     *
     * @param string $key                  The column to set.
     * @param string $value                The value, expression, placeholder, etc.
     * @param bool   $createNamedParameter Automatically create a named parameter for the value
     *
     * @return QueryBuilder This QueryBuilder instance.
     */
    public function set(string $key, $value, bool $createNamedParameter = true): CoreQueryBuilder
    {
        if ('uid' === $key && $this->shouldTableBeSequenced()) {
            throw new \InvalidArgumentException('no uid allowed in update statement!', 1564122229);
        }

        parent::set($key, $value, $createNamedParameter);

        return $this;
    }

    /**
     * Specifies values for an insert query indexed by column names.
     * Replaces any previous values, if any.
     *
     * @param array $values                The values to specify for the insert query indexed by column names.
     * @param bool  $createNamedParameters Automatically create named parameters for all values
     *
     * @return QueryBuilder This QueryBuilder instance.
     */
    public function values(array $values, bool $createNamedParameters = true): CoreQueryBuilder
    {
        parent::values(
            $this->getTypo3Service()->modifyInsertFields($this->sanitizeTableName($this->concreteQueryBuilder->getQueryPart('from')['table']), $values),
            $createNamedParameters
        );

        return $this;
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
            $this->typo3Service = GeneralUtility::makeInstance(Typo3Service::class, new Sequenzer());
        }

        return $this->typo3Service;
    }

    /**
     * Determines the defined table name without quotation marks (`).
     *
     * @param string $tableName
     * @return string
     */
    protected function sanitizeTableName(string $tableName): string
    {
        $mark = '`';
        if (!empty($tableName) && $tableName[0] === $mark && $tableName[strlen($tableName) - 1] === $mark) {
            return str_replace($mark, '', $tableName);
        }

        return $tableName;
    }

    /**
     * @return bool
     */
    protected function shouldTableBeSequenced(): bool
    {
        return $this->getTypo3Service()->needsSequenzer($this->sanitizeTableName($this->concreteQueryBuilder->getQueryPart('from')['table']));
    }
}
