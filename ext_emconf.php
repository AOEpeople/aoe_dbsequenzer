<?php

########################################################################
# Extension Manager/Repository config file for ext "aoe_dbsequenzer".
#
# Auto generated 30-11-2012 10:55
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'AOE Database Sequenzer',
	'description' => 'With this extension you can ensure diffrent unique keys for the configured tables',
	'category' => 'misc',
	'author' => 'Dev-Team AOE',
	'author_email' => 'dev@aoe.com',
	'shy' => '',
	'dependencies' => 'extbase',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => '',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => 'AOE GmbH',
	'version' => '0.2.4',
	'constraints' => array(
		'depends' => array(
		    't3p_scalable' => '1.5.0',
			'extbase' => '1.3.0',
            'php' => '5.3.0',
            'typo3' => '6.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:23:{s:21:"ext_conf_template.txt";s:4:"50e4";s:17:"ext_localconf.php";s:4:"a2a0";s:26:"Tests/TYPO3ServiceTest.php";s:4:"20b6";s:17:"Tests/phpunit.xml";s:4:"849f";s:18:"Tests/BaseTest.php";s:4:"88f9";s:40:"Tests/OverwriteProtectionServiceTest.php";s:4:"fd25";s:41:"Resources/Private/Templates/formField.php";s:4:"1bf5";s:82:"Resources/Private/Language/locallang_csh_tx_aoedbsequenzer_overwriteprotection.xml";s:4:"55ca";s:43:"Resources/Private/Language/locallang_db.xml";s:4:"abc3";s:64:"Resources/Public/Icons/tx_aoedbsequenzer_overwriteprotection.gif";s:4:"311a";s:9:"ChangeLog";s:4:"8696";s:12:"ext_icon.gif";s:4:"f19a";s:14:"ext_tables.sql";s:4:"78a8";s:41:"Configuration/TCA/Overwriteprotection.php";s:4:"d488";s:14:"ext_tables.php";s:4:"736b";s:32:"Classes/class.ux_ux_t3lib_db.php";s:4:"3d8b";s:38:"Classes/OverwriteProtectionService.php";s:4:"8909";s:29:"Classes/class.ux_t3lib_db.php";s:4:"ed43";s:59:"Classes/Domain/Repository/OverwriteprotectionRepository.php";s:4:"0320";s:44:"Classes/Domain/Model/Overwriteprotection.php";s:4:"a982";s:24:"Classes/TYPO3Service.php";s:4:"68cf";s:21:"Classes/Sequenzer.php";s:4:"acd9";s:10:"README.txt";s:4:"ee2d";}',
);

?>