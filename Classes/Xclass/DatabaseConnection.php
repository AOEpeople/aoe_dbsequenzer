<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 AOE GmbH (dev@aoe.com)
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
 *
 * @author danielpotzinger
 *
 */
class Tx_AoeDbsequenzer_Xclass_DatabaseConnection extends \Aoe\T3p_scalable\Xclass\DatabaseConnection {
	/**
	 * @var boolean
	 */
	private $isEnabled = TRUE;
    /**
     * @var Tx_AoeDbsequenzer_TYPO3Service
     */
    private $TYPO3Service;

	/**
	 * Enables the sequencer.
	 *
	 * @return void
	 */
	public function enable() {
		$this->isEnabled = TRUE;
	}

	/**
	 * Disables the sequencer.
	 *
	 * @return void
	 */
	public function disable() {
		$this->isEnabled = FALSE;
	}

	/**
	 * Creates and executes an INSERT SQL-statement for $table from the array with field/value pairs $fields_values.
	 * Using this function specifically allows us to handle BLOB and CLOB fields depending on DB
	 *
	 * @param	string		$table Table name
	 * @param	array		$fields_values Field values as key=>value pairs. Values will be escaped internally. Typically you would fill an array like "$insertFields" with 'fieldname'=>'value' and pass it to this function as argument.
	 * @param	string/array $no_quote_fields See fullQuoteArray()
	 * @return	string|NULL Full SQL query for INSERT, NULL if $fields_values is empty
	 */
	function INSERTquery($table, $fields_values, $no_quote_fields = FALSE) {
		if ($this->isEnabled) {
			$fields_values = $this->getTYPO3Service()->modifyInsertFields($table, $fields_values);
		}
		return parent::INSERTquery($table, $fields_values, $no_quote_fields);
	}

	/**
	 * Creates an INSERT SQL-statement for $table with multiple rows.
	 *
	 * @param	string		      $table Table name
	 * @param	array		      $fields Field names
	 * @param	array		      $rows Table rows. Each row should be an array with field values mapping to $fields
	 * @param	bool|array|string $no_quote_fields See fullQuoteArray()
	 * @return	string		      Full SQL query for INSERT (unless $rows does not contain any elements in which case it will be false)
	 */
	public function INSERTmultipleRows($table, array $fields, array $rows, $no_quote_fields = FALSE) {
		if ($this->isEnabled) {
			foreach ($rows as &$row) {
				$row = $this->getTYPO3Service()->modifyInsertFields($table, $row);
			}
		}
		return parent::INSERTmultipleRows($table, $fields, $rows, $no_quote_fields);
	}

	/**
	 * Creates an UPDATE SQL-statement for $table where $where-clause (typ. 'uid=...') from the array with field/value pairs $fields_values.
	 *
	 * @param	string		$table See exec_UPDATEquery()
	 * @param	string		$where See exec_UPDATEquery()
	 * @param	array		$fields_values See exec_UPDATEquery()
	 * @param	array		$no_quote_fields See fullQuoteArray()
	 * @return	string		Full SQL query for UPDATE (unless $fields_values does not contain any elements in which case it will be false)
	 */
	public function UPDATEquery($table, $where, $fields_values, $no_quote_fields = FALSE) {
		if ($this->getTYPO3Service()->needsSequenzer($table) && isset($fields_values['uid'])) {
			throw new InvalidArgumentException('no uid allowed in update statement!');
		}
		return parent::UPDATEquery($table, $where, $fields_values, $no_quote_fields);
	}

	 /**
	 * Creates and executes a DELETE SQL-statement for $table where $where-clause
	 *
	 * @param	string		$table Database tablename
	 * @param	string		$where WHERE clause, eg. "uid=1". NOTICE: You must escape values in this argument with $this->fullQuoteStr() yourself!
	 * @return boolean|\mysqli_result|object MySQLi result object / DBAL object
	 */
	public function exec_DELETEquery($table, $where) {
		//TODO: log deletes
		return parent::exec_DELETEquery($table, $where);
	}

	/**
	 * Open a (persistent) connection to a MySQL server
	 *
     * @param string $host Deprecated since 6.1, will be removed in two versions. Database host IP/domain[:port]
     * @param string $username Deprecated since 6.1, will be removed in two versions. Username to connect with.
     * @param string $password Deprecated since 6.1, will be removed in two versions. Password to connect with.
	 * @return \mysqli|NULL	Returns current database handle
	 */
	function sql_pconnect($host = null, $username = null, $password = null)	{
		parent::sql_pconnect();
		$this->getTYPO3Service()->setDbLink($this->getDatabaseHandle());
		return $this->getDatabaseHandle();
	}

    /**
     * create instance of Tx_AoeDbsequenzer_TYPO3Service by lazy-loading
     *
     * Why we do this?
     * Because some unittests backup the variable $GLOBALS (and so, also the variable $GLOBALS['TYPO3_DB']), which means, that this
     * object/class will be serialized/unserialized, so the instance of Tx_AoeDbsequenzer_TYPO3Service will be null after unserialization!
     *
     * @return Tx_AoeDbsequenzer_TYPO3Service
     */
    protected function getTYPO3Service() {
        if (false === isset($this->TYPO3Service)) {
            $this->TYPO3Service = new Tx_AoeDbsequenzer_TYPO3Service(new Tx_AoeDbsequenzer_Sequenzer());
        }
        return $this->TYPO3Service;
    }
}
