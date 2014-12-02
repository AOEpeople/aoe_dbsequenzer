<?php
if (!isset($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['t3lib/class.t3lib_db.php'])) {
	$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['t3lib/class.t3lib_db.php'] = t3lib_extMgm::extPath($_EXTKEY) . 'Classes/class.ux_t3lib_db.php';
} else {
	$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3p_scalable/class.ux_t3lib_db.php'] = t3lib_extMgm::extPath($_EXTKEY) . 'Classes/class.ux_ux_t3lib_db.php';
}

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['Tx_T3pScalable_Xclass_DatabaseConnection'] = array(
    'className' => 'Tx_AoeDbsequenzer_Xclass_DatabaseConnection',
);
