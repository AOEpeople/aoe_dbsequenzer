<?php
/***************************************************************
 * Extension Manager/Repository config file for ext "aoe_dbsequenzer".
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'AOE Database Sequenzer',
	'description' => 'With this extension you can ensure different unique keys for the configured tables',
	'category' => 'misc',
	'author' => 'Dev-Team AOE',
	'author_company' => 'AOE GmbH',
	'author_email' => 'dev@aoe.com',
	'shy' => '',
	'dependencies' => 'extbase',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'version' => '2.1.0',
	'constraints' => array(
		'depends' => array(
			'extbase' => '1.3.0',
            'typo3' => '6.2.0-7.6.99',
		),
		'conflicts' => array(),
		'suggests' => array(),
	),
	'_md5_values_when_last_written' => '',
);
