<?php
declare(strict_types = 1);
namespace Aoe\AoeDbSequenzer\Xclass;

/*
 * This file xclasses and extends a part of the TYPO3 CMS project.
 */

use Aoe\AoeDbSequenzer\Sequenzer;
use Aoe\AoeDbSequenzer\Service\Typo3Service;
use TYPO3\CMS\Core\Database\Query\QueryBuilder as CoreQueryBuilder;

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
     * @param string $key The column to set.
     * @param string $value The value, expression, placeholder, etc.
     * @param bool $createNamedParameter Automatically create a named parameter for the value
     *
     * @return QueryBuilder This QueryBuilder instance.
     */
    public function set(string $key, $value, bool $createNamedParameter = true): CoreQueryBuilder
    {
        if ('uid' === $key && $this->getTypo3Service()->needsSequenzer($this->concreteQueryBuilder->getQueryPart('from')['table'])) {
            throw new \InvalidArgumentException('no uid allowed in update statement!', 1512378277);
        }

        $this->concreteQueryBuilder->set(
            $this->quoteIdentifier($key),
            $createNamedParameter ? $this->createNamedParameter($value) : $value
        );

        return $this;
    }

    /**
     * Sets a value for a column in an insert query.
     *
     * @param string $column The column into which the value should be inserted.
     * @param string $value The value that should be inserted into the column.
     * @param bool $createNamedParameter Automatically create a named parameter for the value
     *
     * @return QueryBuilder This QueryBuilder instance.
     */
    public function setValue(string $column, $value, bool $createNamedParameter = true): CoreQueryBuilder
    {
        if ('uid' === $column) {
            $value = $this->getTypo3Service()->modifyField($this->concreteQueryBuilder->getQueryPart('from')['table'], $column, $value);
        }

        $this->concreteQueryBuilder->setValue(
            $this->quoteIdentifier($column),
            $createNamedParameter ? $this->createNamedParameter($value) : $value
        );

        return $this;
    }

    /**
     * Specifies values for an insert query indexed by column names.
     * Replaces any previous values, if any.
     *
     * @param array $values The values to specify for the insert query indexed by column names.
     * @param bool $createNamedParameters Automatically create named parameters for all values
     *
     * @return QueryBuilder This QueryBuilder instance.
     */
    public function values(array $values, bool $createNamedParameters = true): CoreQueryBuilder
    {
        $values = $this->getTypo3Service()->modifyInsertFields($this->concreteQueryBuilder->getQueryPart('from')['table'], $values);

        if ($createNamedParameters === true) {
            foreach ($values as &$value) {
                $value = $this->createNamedParameter($value);
            }
        }

        $this->concreteQueryBuilder->values($this->quoteColumnValuePairs($values));

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
            $this->typo3Service = new Typo3Service(new Sequenzer());
        }
        return $this->typo3Service;
    }
}
