<?php
if (! defined ( 'TYPO3_MODE' )) {
    die ( 'Access denied.' );
}

if (TYPO3_MODE == 'BE') {
	$config = unserialize ( $GLOBALS ['TYPO3_CONF_VARS'] ['EXT'] ['extConf'] ['aoe_dbsequenzer'] );
	$tables = array ();
	if (isset ( $config ['tables'] )) {
		$tables = explode ( ',', $config ['tables'] );
	}
	$columnConfig = array ();
	$columnConfig ['tx_aoe_dbsquenzer_protectoverwrite_till'] = array ();
	$columnConfig ['tx_aoe_dbsquenzer_protectoverwrite_till'] ['label'] = 'LLL:EXT:aoe_dbsequenzer/Resources/Private/Language/locallang_db.xml:protectoverwrite_till';
	$columnConfig ['tx_aoe_dbsquenzer_protectoverwrite_till'] ['config'] = array ();
	$columnConfig ['tx_aoe_dbsquenzer_protectoverwrite_till'] ['config'] ['type'] = 'user';
	$columnConfig ['tx_aoe_dbsquenzer_protectoverwrite_till'] ['config'] ['userFunc'] = 'Tx_AoeDbsequenzer_OverwriteProtectionService->renderInput';
	$columnConfig ['tx_aoe_dbsquenzer_protectoverwrite_till'] ['config'] ['eval'] = 'datetime';

	$columnName = 'tx_aoe_dbsquenzer_protectoverwrite_till';

	foreach ( $tables as $table ) {
		// add columnsConfig at END of TCA-configuration
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns ( $table, $columnConfig);
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes ( $table, $columnName );

		// move columnsConfig from END of TCA-configuration to BEGIN of TCA-configuration
		if(is_array($GLOBALS['TCA'][$table]['types'])) {
			foreach($GLOBALS['TCA'][$table]['types'] as &$tableTypeConfig) {
				if(array_key_exists('showitem', $tableTypeConfig) && preg_match('/'.$columnName.'$/i', $tableTypeConfig['showitem'])) {
					$showItems = &$tableTypeConfig['showitem'];

					// 1. delete columnsConfig at END of TCA-configuration
					$showItems = preg_replace('/,\s?'.$columnName.'/i', '', $showItems);

					// 2. add columnsConfig at BEGIN of TCA-configuration
					if(preg_match('/^--div--/i', $showItems)) {
						// first entry is an tab
						$firstColumnEntry = substr($showItems, 0, stripos($showItems, ',') + 1);
						$showItems = str_replace($firstColumnEntry, '', $showItems);
						$showItems = $firstColumnEntry . $columnName . ',' . $showItems;
					} else {
						// first entry is no tab
						$showItems = $columnName . ',' . $showItems;
					}
				}
			}
		}
	}

	$GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:aoe_dbsequenzer/Classes/OverwriteProtectionService.php:Tx_AoeDbsequenzer_OverwriteProtectionService';
	$GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = 'EXT:aoe_dbsequenzer/Classes/OverwriteProtectionService.php:Tx_AoeDbsequenzer_OverwriteProtectionService';
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_aoedbsequenzer_domain_model_overwriteprotection');
}
