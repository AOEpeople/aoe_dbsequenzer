<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\\CMS\\Core\\Database\\DatabaseConnection'] = array(
    'className' => 'Tx_AoeDbsequenzer_Xclass_DatabaseConnection',
);

/** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);
$signalSlotDispatcher->connect(
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::class,
    'tcaIsBeingBuilt',
    \Tx_AoeDbsequenzer_TcaPostProcessor::class,
    'postProcessTca'
);
