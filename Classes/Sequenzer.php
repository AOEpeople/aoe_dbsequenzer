<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2009 AOE media GmbH <dev@aoemedia.de>
 * All rights reserved
 *
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Sequenzer is used to generate system wide independet IDs
 * 
 * @author danielpotzinger
 *
 */
class Tx_AoeDbsequenzer_Sequenzer {
	
	private $table = 'tx_aoedbsequenzer_sequenz';
	private $dbLink;
	private $defaultStart = 0;
	private $defaultOffset = 1;
	
	/**
	 * @param $defaultStart the $defaultStart to set
	 */
	public function setDefaultStart($defaultStart) {
		$this->defaultStart = $defaultStart;
	}
	
	/**
	 * @param $defaultOffset the $defaultOffset to set
	 */
	public function setDefaultOffset($defaultOffset) {
		$this->defaultOffset = $defaultOffset;
	}
	
	/**
	 * sets mysql dblink with DB connection
	 * 
	 * @param resource $dbLink optional
	 */
	public function setDbLink($dbLink = NULL) {
		if (is_null ( $dbLink )) {
			$this->dbLink = $GLOBALS ['TYPO3_DB']->link;
		} else {
			$this->dbLink = $dbLink;
		}
	}
	
	/**
	 * returns next free id in the sequenz of the table
	 * 
	 * @param unknown_type $table
	 * @param unknown_type $depth
	 */
	public function getNextIdForTable($table, $depth = 0) {
		if ($depth > 99) {
			throw new Exception ( 'The sequenzer cannot return IDs for this table -' . $table . ' Too many recursions - maybe to much load?' );
		}
		
		$result = mysql_query ( 'SELECT * FROM ' . $this->getTable () . ' WHERE tablename=\'' . $this->escapeString ( $table ) . '\'', $this->dbLink );
		//echo 'SELECT * FROM '.$this->getTable().' WHERE tablename=\''.$this->escapeString($table).'\'';
		$row = mysql_fetch_assoc ( $result );
		
		if (! isset ( $row ['current'] )) {
			$this->initSequenzerForTable ( $table );
			return $this->getNextIdForTable ( $table, ++ $depth );
			//throw new Exception('The sequenzer cannot return IDs for this table -'.$table.'- its not configured!');
		}
		$new = $row ['current'] + $row ['offset'];
		$updateTimeStamp = time ();
		$res2 = mysql_query ( 'UPDATE ' . $this->getTable () . ' SET current=' . $new . ', timestamp=' . $updateTimeStamp . ' WHERE timestamp=' . $row ['timestamp'] . ' AND tablename=\'' . $this->escapeString ( $table ) . '\'', $this->dbLink );
		if ($res2 && mysql_affected_rows ( $this->dbLink ) > 0) {
			return $new;
		} else {
			return $this->getNextIdForTable ( $table, ++ $depth );
		}
	}
	
	/**
	 * if no sehduler entry for the table yet exists, this method initialises the sequenzer to fit offest and start and current max value in the table
	 * 
	 * @param string $table
	 */
	private function initSequenzerForTable($table) {
		$result = mysql_query ( 'SELECT max(uid) as max FROM ' . $table, $this->dbLink );
		$row = mysql_fetch_assoc ( $result );
		$currentMax = $row ['max'] + 10;
		
		$start = $this->defaultStart + ($this->defaultOffset * ceil ( $currentMax / $this->defaultOffset ));
		$insert = 'INSERT INTO ' . $this->getTable () . ' ( tablename, current, offset, timestamp ) VALUES ( \'' . $this->escapeString ( $table ) . '\', ' . $start . ',' . intval ( $this->defaultOffset ) . ',' . time () . ' )';
		mysql_query ( $insert, $this->dbLink );
	
	}
	
	/**
	 * get sheduler tablename
	 * @return string
	 */
	private function getTable() {
		return $this->table;
	}
	
	/**
	 * 
	 * @param string $string
	 */
	private function escapeString($string) {
		return mysql_escape_string ( $string );
	}

}
