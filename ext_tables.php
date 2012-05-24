<?php
if (! defined ( 'TYPO3_MODE' ))
	die ( 'Access denied.' );
if (TYPO3_MODE == 'BE') {
	require_once t3lib_extMgm::extPath($_EXTKEY).'Classes/OverwriteProtectionService.php';
	$config = unserialize ( $GLOBALS ['TYPO3_CONF_VARS'] ['EXT'] ['extConf'] ['aoe_dbsequenzer'] );
	$tables = array ();
	if (isset ( $config ['tables'] )) {
		$tables = explode ( ',', $config ['tables'] );
	}
	$column = array ();
	$column ['tx_aoe_dbsquenzer_protectoverwrite_till'] = array ();
	$column ['tx_aoe_dbsquenzer_protectoverwrite_till'] ['label'] = 'LLL:EXT:aoe_dbsequenzer/Resources/Private/Language/locallang_db.xml:protectoverwrite_till';
	$column ['tx_aoe_dbsquenzer_protectoverwrite_till'] ['config'] = array ();
	
	$column ['tx_aoe_dbsquenzer_protectoverwrite_till'] ['config'] ['type'] = 'user';
	$column ['tx_aoe_dbsquenzer_protectoverwrite_till'] ['config'] ['userFunc'] = 'Tx_AoeDbsequenzer_OverwriteProtectionService->renderInput';
	$column ['tx_aoe_dbsquenzer_protectoverwrite_till'] ['config'] ['eval'] = 'datetime';
	foreach ( $tables as $table ) {
		t3lib_extMgm::addTCAcolumns ( $table, $column, 1 );
		t3lib_extMgm::addToAllTCAtypes ( $table, 'tx_aoe_dbsquenzer_protectoverwrite,tx_aoe_dbsquenzer_protectoverwrite_till' );
	}
	
	$GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:aoe_dbsequenzer/Classes/OverwriteProtectionService.php:Tx_AoeDbsequenzer_OverwriteProtectionService';
	$GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = 'EXT:aoe_dbsequenzer/Classes/OverwriteProtectionService.php:Tx_AoeDbsequenzer_OverwriteProtectionService';
	t3lib_extMgm::allowTableOnStandardPages('tx_aoedbsequenzer_domain_model_overwriteprotection');
	$TCA['tx_aoedbsequenzer_domain_model_overwriteprotection'] = array (
		'ctrl' => array (
			'title'				=> 'LLL:EXT:aoe_dbsequenzer/Resources/Private/Language/locallang_db.xml:tx_aoedbsequenzer_overwriteprotection',
			'label' 			=> 'protected_uid',
			'label_alt'			=> 'protected_tablename',
			'label_alt_force'	=> true,
			'tstamp' 			=> 'tstamp',
			'crdate' 			=> 'crdate',
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/Overwriteprotection.php',
			'iconfile' 			=> t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_aoedbsequenzer_overwriteprotection.gif'
		)
	);
}
