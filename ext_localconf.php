<?php
defined('TYPO3_MODE') or die();

if (empty($GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['wrapperClass'])) {
    $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['wrapperClass'] = \Aoe\AoeDbSequenzer\Xclass\Connection::class;
}

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\\CMS\\Core\\Database\\Query\\QueryBuilder'] = [
    'className' => 'Aoe\\AoeDbSequenzer\\Xclass\\QueryBuilder'
];
