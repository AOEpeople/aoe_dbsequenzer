<?php
require_once(t3lib_extMgm::extPath($_EXTKEY).'Classes/Sequenzer.php');
require_once(t3lib_extMgm::extPath($_EXTKEY).'Classes/TYPO3Service.php');

if (!isset($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['t3lib/class.t3lib_db.php'])) {
	$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['t3lib/class.t3lib_db.php'] = t3lib_extMgm::extPath($_EXTKEY) . 'Classes/class.ux_t3lib_db.php';
}
else {
	$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3p_scalable/typo3versions/4.3.FF/class.ux_t3lib_db.php'] = t3lib_extMgm::extPath($_EXTKEY) . 'Classes/class.ux_ux_t3lib_db.php';
}