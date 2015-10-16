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
 * @package aoe_dbsequenzer
 */
class Tx_AoeDbsequenzer_TYPO3Service {
	/**
	 * @var Tx_AoeDbsequenzer_Sequenzer
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
	 *
	 * @param Tx_AoeDbsequenzer_Sequenzer $sequenzer
	 */
	public function __construct(Tx_AoeDbsequenzer_Sequenzer $sequenzer, $conf = NULL) {
		$this->sequenzer = $sequenzer;
		if (is_null ( $conf )) {
			$this->conf = unserialize ( $GLOBALS ['TYPO3_CONF_VARS'] ['EXT'] ['extConf'] ['aoe_dbsequenzer'] );
		} else {
			$this->conf = $conf;
		}
		$this->sequenzer->setDefaultOffset ( intval ( $this->conf ['offset'] ) );
		$this->sequenzer->setDefaultStart ( intval ( $this->conf ['system'] ) );
		$explodedValues = explode ( ',', $this->conf ['tables'] );
		$this->supportedTables = array_map ( 'trim', $explodedValues );
	}
	
	/**
	 * sets the db link
	 *
	 * @param resource $link
	 */
	public function setDbLink($link) {
		$this->sequenzer->setDbLink ( $link );
	}
	
	/**
	 * Modify a TYPO3 insert array (key -> value) , and adds the uid that should be forced during INSERT
	 *
	 * @param string $tableName
	 * @param array $fields_values
	 */
	public function modifyInsertFields($tableName, array $fields_values) {
		if ($this->needsSequenzer ( $tableName )) {
			if (isset ( $fields_values ['uid'] )) {
				t3lib_div::devLog ( 'UID is already set for table "' . $tableName . '"', 'aoe_dbsequenzer', 2, $fields );
			} else {
				$fields_values ['uid'] = $this->sequenzer->getNextIdForTable ( $tableName );
			}
		}
		return $fields_values;
	}
	/**
	 * If a table is configured to use the sequenzer
	 *
	 * @param string $tableName
	 * @return boolean
	 */
	public function needsSequenzer($tableName) {
		if (in_array ( $tableName, $this->supportedTables )) {
			return true;
		}
		return false;
	}

}
