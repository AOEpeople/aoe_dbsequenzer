<?php
if (! defined ( 'TYPO3_MODE' )) {
    die ( 'Access denied.' );
}

if (TYPO3_MODE == 'BE') {
	$GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
        \Aoe\AoeDbSequenzer\Service\OverwriteProtectionService::class;

	$GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] =
        \Aoe\AoeDbSequenzer\Service\OverwriteProtectionService::class;

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_aoedbsequenzer_domain_model_overwriteprotection');
}
