<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

return array(
    'ctrl' => array (
        'title'	=> 'LLL:EXT:aoe_dbsequenzer/Resources/Private/Language/locallang_db.xml:tx_aoedbsequenzer_overwriteprotection',
        'label' => 'protected_uid',
        'label_alt'	=> 'protected_tablename',
        'label_alt_force'=> true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('aoe_dbsequenzer').
            'Resources/Public/Icons/tx_aoedbsequenzer_overwriteprotection.gif'
    ),
    'interface' => array(
        'showRecordFieldList' => 'protected_uid,protected_tablename,protected_time,protected_mode'
    ),
    'columns' => array(
        'pid' => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.pid',
            'config'  => array(
                'type' => 'passthrough'
            )
        ),
        'protected_uid' => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:aoe_dbsequenzer/Resources/Private/Language/locallang_db.xml:protected_uid',
            'config'  => array(
                'type' => 'input',
            )
        ),
        'protected_tablename' => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:aoe_dbsequenzer/Resources/Private/Language/locallang_db.xml:protected_tablename',
            'config'  => array(
                'type' => 'input',
            )
        ),
        'protected_time' => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:aoe_dbsequenzer/Resources/Private/Language/locallang_db.xml:protected_time',
            'config'  => array(
                'type' => 'input',
                'eval' => 'date'
            )
        ),
        'protected_mode' => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:aoe_dbsequenzer/Resources/Private/Language/locallang_db.xml:protected_mode',
            'config'  => array(
                'type' => 'input',
            )
        ),
    ),
    'types' => array(
        '1' => array('showitem' => 'protected_uid,protected_tablename,protected_time,protected_mode')
    ),
    'palettes' => array(
        '1' => array('showitem' => 'protected_uid,protected_tablename,protected_time,protected_mode')
    )
);