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
	/**
	 * @var string
	 */
	private $table = 'tx_aoedbsequenzer_sequenz';
	/**
	 * @var recource
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
		
		$result = $this->query ( 'SELECT * FROM ' . $this->getTable () . ' WHERE tablename=\'' . $this->escapeString ( $table ) . '\'' );
		//echo 'SELECT * FROM '.$this->getTable().' WHERE tablename=\''.$this->escapeString($table).'\'';
		$row = mysql_fetch_assoc ( $result );
		
		if (! isset ( $row ['current'] )) {
			$this->initSequenzerForTable ( $table );
			return $this->getNextIdForTable ( $table, ++ $depth );
			//throw new Exception('The sequenzer cannot return IDs for this table -'.$table.'- its not configured!');
		} elseif ($row ['timestamp'] + $this->checkInterval < $GLOBALS ['EXEC_TIME']) {
			$defaultStartValue = $this->getDefaultStartValue ( $table );
			$isValueOutdated = ($row ['current'] < $defaultStartValue);
			$isOffsetChanged = ($row ['offset'] != $this->defaultOffset);
			$isStartChanged = ($row ['current'] % $this->defaultOffset != $this->defaultStart);
			if ($isValueOutdated || $isOffsetChanged || $isStartChanged) {
				$row ['current'] = $defaultStartValue;
			}
		}
		
		$new = $row ['current'] + $row ['offset'];
		$updateTimeStamp = $GLOBALS ['EXEC_TIME'];
		$res2 = $this->query ( 'UPDATE ' . $this->getTable () . ' SET current=' . $new . ', timestamp=' . $updateTimeStamp . ' WHERE timestamp=' . $row ['timestamp'] . ' AND tablename=\'' . $this->escapeString ( $table ) . '\'' );
		if ($res2 && mysql_affected_rows ( $this->dbLink ) > 0) {
			return $new;
		} else {
			return $this->getNextIdForTable ( $table, ++ $depth );
		}
	}
	
	/**
	 * Gets the default start value for a given table.
	 *
	 * @param string $table
	 * @param integer
	 */
	private function getDefaultStartValue($table) {
		$result = $this->query ( 'SELECT max(uid) as max FROM ' . $table );
		$row = mysql_fetch_assoc ( $result );
		$currentMax = $row ['max'] + 1;
		$start = $this->defaultStart + ($this->defaultOffset * ceil ( $currentMax / $this->defaultOffset ));
		
		return $start;
	}
	
	/**
	 * if no sehduler entry for the table yet exists, this method initialises the sequenzer to fit offest and start and current max value in the table
	 *
	 * @param string $table
	 */
	private function initSequenzerForTable($table) {
		$start = $this->getDefaultStartValue ( $table );
		$insert = 'INSERT INTO ' . $this->getTable () . ' ( tablename, current, offset, timestamp ) VALUES ( \'' . $this->escapeString ( $table ) . '\', ' . $start . ',' . intval ( $this->defaultOffset ) . ',' . $GLOBALS ['EXEC_TIME'] . ' )';
		$this->query ( $insert );
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
	/**
	 * @param string $sql
	 * @return recource
	 * @throws Exception
	 */
	private function query($sql) {
		$result = mysql_query ( $sql, $this->dbLink );
		if (mysql_error ( $this->dbLink )) {
			throw new Exception ( mysql_error ( $this->dbLink ), mysql_errno ( $this->dbLink ) );
		}
		return $result;
	}

}
