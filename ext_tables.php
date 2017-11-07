<?php
if (! defined ( 'TYPO3_MODE' )) {
    die ( 'Access denied.' );
}

if (TYPO3_MODE == 'BE') {
	$GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:aoe_dbsequenzer/Classes/OverwriteProtectionService.php:Tx_AoeDbsequenzer_OverwriteProtectionService';
	$GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = 'EXT:aoe_dbsequenzer/Classes/OverwriteProtectionService.php:Tx_AoeDbsequenzer_OverwriteProtectionService';
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_aoedbsequenzer_domain_model_overwriteprotection');
}
