<?php

/**
 * 
 * @author danielpotzinger
 *
 */
class ux_ux_t3lib_db extends ux_t3lib_db {
	
	private $TYPO3Service;
	
	/**
	 * 
	 */
	public function __construct() {
		parent::__construct();
		$this->TYPO3Service = new Tx_AoeDbsequenzer_TYPO3Service(new Tx_AoeDbsequenzer_Sequenzer());
	}
	
	/**
	 * Creates and executes an INSERT SQL-statement for $table from the array with field/value pairs $fields_values.
	 * Using this function specifically allows us to handle BLOB and CLOB fields depending on DB
	 * Usage count/core: 47
	 *
	 * @param	string		Table name
	 * @param	array		Field values as key=>value pairs. Values will be escaped internally. Typically you would fill an array like "$insertFields" with 'fieldname'=>'value' and pass it to this function as argument.
	 * @param	string/array		See fullQuoteArray()
	 * @return	pointer		MySQL result pointer / DBAL object
	 */
	function INSERTquery($table, $fields_values, $no_quote_fields = FALSE) {
		$fields_values = $this->TYPO3Service->modifyInsertFields($table, $fields_values);				
		return parent::INSERTquery($table, $fields_values, $no_quote_fields);
	}
	
	
	
	/**
	 * Creates an INSERT SQL-statement for $table with multiple rows.
	 *
	 * @param	string		Table name
	 * @param	array		Field names
	 * @param	array		Table rows. Each row should be an array with field values mapping to $fields
	 * @param	string/array		See fullQuoteArray()
	 * @return	string		Full SQL query for INSERT (unless $rows does not contain any elements in which case it will be false)
	 */
	public function INSERTmultipleRows($table, array $fields, array $rows, $no_quote_fields = FALSE) {
			foreach ($rows as $row) {
				$row = $this->TYPO3Service->modifyInsertFields($table, $row);				
			}		
			return parent::INSERTmultipleRows($table, $fields, $rows, $no_quote_fields);	
	}
	
	/**
	 * Creates an UPDATE SQL-statement for $table where $where-clause (typ. 'uid=...') from the array with field/value pairs $fields_values.
	 * Usage count/core: 6
	 *
	 * @param	string		See exec_UPDATEquery()
	 * @param	string		See exec_UPDATEquery()
	 * @param	array		See exec_UPDATEquery()
	 * @param	array		See fullQuoteArray()
	 * @return	string		Full SQL query for UPDATE (unless $fields_values does not contain any elements in which case it will be false)
	 */
	public function UPDATEquery($table, $where, $fields_values, $no_quote_fields = FALSE) {
		if (isset($fields_values['uid'])) {
			throw new InvalidArgumentException('no uid allowed in update statement!');
		}
		return parent::UPDATEquery($table, $where, $fields_values, $no_quote_fields);		
	}
	
	
	
	 /**
	 * Creates and executes a DELETE SQL-statement for $table where $where-clause
	 * Usage count/core: 40
	 *
	 * @param	string		Database tablename
	 * @param	string		WHERE clause, eg. "uid=1". NOTICE: You must escape values in this argument with $this->fullQuoteStr() yourself!
	 * @return	pointer		MySQL result pointer / DBAL object
	 */
	public function exec_DELETEquery($table, $where) {
		//TODO: log deletes
		return parent::exec_DELETEquery($table, $where);
	}
	
	
	
	/**
	 * Open a (persistent) connection to a MySQL server
	 * mysql_pconnect() wrapper function
	 * Usage count/core: 12
	 *
	 * @param	string		Database host IP/domain
	 * @param	string		Username to connect with.
	 * @param	string		Password to connect with.
	 * @return	pointer		Returns a positive MySQL persistent link identifier on success, or FALSE on error.
	 */
	function sql_pconnect($TYPO3_db_host, $TYPO3_db_username, $TYPO3_db_password)	{
		parent::sql_pconnect($TYPO3_db_host, $TYPO3_db_username, $TYPO3_db_password);
		$this->TYPO3Service->setDbLink($this->link);
		return $this->link;
	}
	
	
}
