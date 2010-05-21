<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 AOE media GmbH <dev@aoemedia.de>
*  All rights reserved
*
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Sequenzer is used to generate system wide independet IDs
 * 
 * @author danielpotzinger
 *
 */
class Tx_AoeDbsequenzer_TYPO3Service {
	
	private $table='sequenzer';
	private $sequenzer;
	
	/**
	 * 
	 * @param Tx_AoeDbsequenzer_Sequenzer $sequenzer
	 */
	public function __construct(Tx_AoeDbsequenzer_Sequenzer $sequenzer) {
		$this->sequenzer = $sequenzer;
		$conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['aoe_dbsequenzer']);
		$this->sequenzer->setDefaultOffset(intval($conf['offset']));
		$this->sequenzer->setDefaultStart(intval($conf['system']));
	}
	
	/**
	 * sets the db link
	 * @param unknown_type $link
	 */
	public function setDbLink($link) {
		$this->sequenzer->setDbLink($link);
	}
	
	/**
	 * 
	 * @param string $tableName
	 * @param array $fields_values
	 */
	public function modifyFields($tableName, array $fields_values) {
		if ($this->needsSequenzer($tableName)) {
			if (isset($fields_values['uid'])) {
				//warning
				throw new Exception('WARNING: uid is set!!!! UID:'.$fields['uid']);
			}
			$fields_values['uid'] = $this->sequenzer->getNextIdForTable($tableName);		
		}
		return $fields_values;
	}
	

	
	public function needsSequenzer($tableName) {
		if ($tableName=='tt_content' || $tableName=='pages') {
			return true;
		}
		return false;
	}
	
	
}
