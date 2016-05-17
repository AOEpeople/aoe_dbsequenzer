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

use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

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
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
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
		$this->objectManager = GeneralUtility::makeInstance ('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
	}

	/**
	 * Injects ObjectManager instance
	 * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
	 */
	public function injectObjectManager(\TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager)
	{
		$this->objectManager = $objectManager;
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
	 * @param \TYPO3\CMS\Core\DataHandling\DataHandler $tcemain
	 */
	public function processDatamap_preProcessFieldArray(&$incomingFieldArray, $table, $id, \TYPO3\CMS\Core\DataHandling\DataHandler &$tcemain) {
		if (FALSE === $this->needsOverWriteProtection ( $table )) {
			return;
		}

        // check, if overwrite-protection-fields are set:
        // If they are NOT set, it means, that any other extension maybe called the process_datamap!
        if(false === array_key_exists(self::OVERWRITE_PROTECTION_TILL, $incomingFieldArray) ||
           false === array_key_exists(self::OVERWRITE_PROTECTION_MODE, $incomingFieldArray)
        ) {
            return;
        }

		if (FALSE === $this->hasOverWriteProtection ( $incomingFieldArray )) {
			$this->removeOverwriteprotection( $id, $table );
		} else {
			$protection = strtotime ( $incomingFieldArray [self::OVERWRITE_PROTECTION_TILL] );
			$mode = $incomingFieldArray [self::OVERWRITE_PROTECTION_MODE];

			$result = $this->getOverwriteprotectionRepository ()->findByProtectedUidAndTableName ( $id, $table );
			if ($result->count() === 0) {
				/* @var $overwriteprotection Tx_AoeDbsequenzer_Domain_Model_Overwriteprotection */
				$overwriteprotection = $this->objectManager->get ( 'Tx_AoeDbsequenzer_Domain_Model_Overwriteprotection' );
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
	 * @param \TYPO3\CMS\Backend\Form\FormEngine $fob
	 */
	public function renderInput(array $PA, \TYPO3\CMS\Backend\Form\FormEngine $fob) {
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
		/* @var $persistenceManager PersistenceManager */
		$persistenceManager = $this->objectManager->get ( 'TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager' );
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