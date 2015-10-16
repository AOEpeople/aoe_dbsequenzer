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
 * @package aoe_dbsequenzer
 */
class Tx_AoeDbsequenzer_OverwriteProtectionService {
	/**
	 * @var string
	 */
	const OVERWRITE_PROTECTION_TILL = 'tx_aoe_dbsquenzer_protectoverwrite_till';
	/**
	 * @var string
	 */
	const OVERWRITE_PROTECTION_MODE = 'tx_aoe_dbsquenzer_protectoverwrite_mode';
	/**
	 * array of configured tables that should call the sequenzer
	 *
	 * @var array
	 */
	private $supportedTables;
	/**
	 * @var Tx_AoeDbsequenzer_Domain_Repository_OverwriteprotectionRepository
	 */
	private $overwriteprotectionRepository;
	/**
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	private $objectManager;
	
	/**
	 * @param array $conf
	 */
	public function __construct($conf = NULL) {
		if (is_null ( $conf )) {
			$conf = unserialize ( $GLOBALS ['TYPO3_CONF_VARS'] ['EXT'] ['extConf'] ['aoe_dbsequenzer'] );
		}
		$explodedValues = explode ( ',', $conf ['tables'] );
		$this->supportedTables = array_map ( 'trim', $explodedValues );
		$this->objectManager = t3lib_div::makeInstance ( 'Tx_Extbase_Object_ObjectManager' );
	}
	/**
	 * @return Tx_AoeDbsequenzer_Domain_Repository_OverwriteprotectionRepository
	 */
	public function getOverwriteprotectionRepository() {
		if (! isset ( $this->overwriteprotectionRepository )) {
			$this->overwriteprotectionRepository = $this->objectManager->get ( 'Tx_AoeDbsequenzer_Domain_Repository_OverwriteprotectionRepository' );
		}
		return $this->overwriteprotectionRepository;
	}

	/**
	 * Hook for deletes in Typo3 Backend. It also delete all overwrite protection
	 * @param string $command
	 * @param string $table
	 * @param integer $id
	 */
	public function processCmdmap_postProcess($command, $table, $id) {
		if (FALSE === $this->needsOverWriteProtection ( $table )) {
			return;
		}
		if ($command !== 'delete') {
			return;
		}
		$this->removeOverwriteprotection( $id, $table );
	}
	/**
	 * Hook for updates in Typo3 backend
	 * @param array $incomingFieldArray
	 * @param string $table
	 * @param integer $id
	 * @param t3lib_tcemain $tcemain
	 */
	public function processDatamap_preProcessFieldArray(&$incomingFieldArray, $table, $id, t3lib_TCEmain &$tcemain) {
		if (FALSE === $this->needsOverWriteProtection ( $table )) {
			return;
		}

		if (FALSE === $this->hasOverWriteProtection ( $incomingFieldArray )) {
			$this->removeOverwriteprotection( $id, $table );
		} else {
			$protection = strtotime ( $incomingFieldArray [self::OVERWRITE_PROTECTION_TILL] );
			$mode = $incomingFieldArray [self::OVERWRITE_PROTECTION_MODE];

			$result = $this->getOverwriteprotectionRepository ()->findByProtectedUidAndTableName ( $id, $table );
			if (count ( $result ) === 0) {
				/* @var $overwriteprotection Tx_AoeDbsequenzer_Domain_Model_Overwriteprotection */
				$overwriteprotection = $this->objectManager->create ( 'Tx_AoeDbsequenzer_Domain_Model_Overwriteprotection' );
				$overwriteprotection->setProtectedMode ( $mode );
				$overwriteprotection->setPid ( $tcemain->getPID ( $table, $id ) );
				$overwriteprotection->setProtectedTablename ( $table );
				$overwriteprotection->setProtectedUid ( $id );
				$overwriteprotection->setProtectedTime ( $protection );
				$this->getOverwriteprotectionRepository ()->add ( $overwriteprotection );
			} else {
				foreach ( $result as $overwriteprotection ) {
					/* @var $overwriteprotection Tx_AoeDbsequenzer_Domain_Model_Overwriteprotection */
					$overwriteprotection->setProtectedMode ( $mode );
					$overwriteprotection->setProtectedTime ( $protection );
				}
			}
			$this->persistAll();
		}
		unset ( $incomingFieldArray [self::OVERWRITE_PROTECTION_TILL] );
		unset ( $incomingFieldArray [self::OVERWRITE_PROTECTION_MODE] );
	}

	/**
	 * Render Form Field in typo3 backend
	 * @param array $PA
	 * @param t3lib_TCEforms $fob
	 */
	public function renderInput(array $PA, t3lib_TCEforms $fob) {
		$content = file_get_contents ( dirname ( __FILE__ ) . '/../Resources/Private/Templates/formField.php' );
		$content = str_replace ( '###UID###', $PA ['row'] ['uid'], $content );
		$content = str_replace ( '###TABLE###', $PA ['table'], $content );
		$content = str_replace ( '###ID###', uniqid (), $content );
		$result = $this->getOverwriteprotectionRepository ()->findByProtectedUidAndTableName ( $PA ['row'] ['uid'], $PA ['table'] );
		$value = '';
		$overwriteMode = '';
		$conflictMode = '';

		foreach ( $result as $overwriteprotection ) {
			/* @var $overwriteprotection Tx_AoeDbsequenzer_Domain_Model_Overwriteprotection */
			$value = $overwriteprotection->getProtectedTime ();
			$value = date ( 'H:i d-m-Y', $value );
			if ($overwriteprotection->getProtectedMode () === 0) {
				$conflictMode = 'selected="selected"';
			} else {
				$overwriteMode = 'selected="selected"';
			}
		}
		$content = str_replace ( '###VALUE###', $value, $content );
		$content = str_replace ( '###OVERWIRTE_MODE###', $overwriteMode, $content );
		$content = str_replace ( '###CONFLICT_MODE###', $conflictMode, $content );
		$content = str_replace ( '###LABEL_MODE###', $fob->sL('LLL:EXT:aoe_dbsequenzer/Resources/Private/Language/locallang_db.xml:protected_mode'), $content );
		$content = str_replace ( '###LABEL_MODE_CONFLICT###', $fob->sL('LLL:EXT:aoe_dbsequenzer/Resources/Private/Language/locallang_db.xml:mode_conflict'), $content );
		$content = str_replace ( '###LABEL_MODE_OVERWIRTE###', $fob->sL('LLL:EXT:aoe_dbsequenzer/Resources/Private/Language/locallang_db.xml:mode_overwrite'), $content );
		return $content;
	}

	/**
	 * @param Tx_AoeDbsequenzer_Domain_Repository_OverwriteprotectionRepository $overwriteprotectionRepository
	 */
	public function setOverwriteprotectionRepository(Tx_AoeDbsequenzer_Domain_Repository_OverwriteprotectionRepository $overwriteprotectionRepository) {
		$this->overwriteprotectionRepository = $overwriteprotectionRepository;
	}

	/**
	 * @param array $fields_values
	 * @return boolean
	 */
	private function hasOverWriteProtection(array $fields_values) {
		if (isset ( $fields_values [self::OVERWRITE_PROTECTION_TILL] )) {
			$value = trim ( $fields_values [self::OVERWRITE_PROTECTION_TILL] );
			if (FALSE === empty ( $value ) && FALSE !== strtotime ( $value )) {
				return true;
			}
		}
		return false;
	}
	/**
	 * If a table is configured to use the sequenzer
	 *
	 * @param string $tableName
	 * @return boolean
	 */
	private function needsOverWriteProtection($tableName) {
		if ($tableName !== 'tx_aoedbsequenzer_domain_model_overwriteprotection' && in_array ( $tableName, $this->supportedTables )) {
			return true;
		}
		return false;
	}
	/**
	 * persist all changes
	 */
	private function persistAll() {
		/* @var $persistenceManager Tx_Extbase_Persistence_Manager */
		$persistenceManager = $this->objectManager->get ( 'Tx_Extbase_Persistence_Manager' );
		$persistenceManager->persistAll ();
	}
	/**
	 * remove overwriteprotection
	 * 
	 * @param integer $id
	 * @param string $table
	 */
	private function removeOverwriteprotection($id, $table) {
		$result = $this->getOverwriteprotectionRepository ()->findByProtectedUidAndTableName ( $id, $table );
		foreach ( $result as $overwriteprotection ) {
			$this->getOverwriteprotectionRepository ()->remove ( $overwriteprotection );
		}
		$this->persistAll();
	}
}