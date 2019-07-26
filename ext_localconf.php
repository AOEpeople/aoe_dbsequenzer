<?php
defined('TYPO3_MODE') or die();

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\\CMS\\Core\\Database\\Connection'] = [
    'className' => 'Aoe\\AoeDbSequenzer\\Xclass\\Connection'
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\\CMS\\Core\\Database\\Query\\QueryBuilder'] = [
    'className' => 'Aoe\\AoeDbSequenzer\\Xclass\\QueryBuilder'
];

if (TYPO3_MODE === 'BE') {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] =
        \Aoe\AoeDbSequenzer\Command\OverwriteProtectionCommandController::class;
}

/** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);
$signalSlotDispatcher->connect(
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::class,
    'tcaIsBeingBuilt',
    \Aoe\AoeDbSequenzer\TcaPostProcessor::class,
    'postProcessTca'
);
