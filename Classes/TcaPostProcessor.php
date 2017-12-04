<?php
namespace Aoe\AoeDbSequenzer;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 AOE GmbH <dev@aoe.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Aoe\AoeDbSequenzer\Service\OverwriteProtectionService;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * @package Aoe\AoeDbSequenzer
 */
class TcaPostProcessor
{
    /**
     * Add overwrite-protection-field to TCA-fields of DB-tables which support overwrite-protection
     *
     * @param array $tca
     * @return array
     */
    public function postProcessTca(array $tca)
    {
        $GLOBALS['TCA'] = $tca;

        $additionalColumns = [
            'tx_aoe_dbsquenzer_protectoverwrite_till' => [
                'label' => 'LLL:EXT:aoe_dbsequenzer/Resources/Private/Language/locallang_db.xml:protectoverwrite_till',
                'config' => [
                    'type' => 'user',
                    'userFunc' => 'Aoe\AoeDbSequenzer\Form\OverwriteTillElement->render',
                    'eval' => 'datetime'
                ]
            ],
            'tx_aoe_dbsquenzer_protectoverwrite_mode' => [
                'label' => 'LLL:EXT:aoe_dbsequenzer/Resources/Private/Language/locallang_db.xml:protected_mode',
                'config' => [
                    'type' => 'user',
                    'userFunc' => 'Aoe\AoeDbSequenzer\Form\OverwriteModeElement->render',
                ]
            ]
        ];

        $columnNames[] = OverwriteProtectionService::OVERWRITE_PROTECTION_TILL;
        $columnNames[] = OverwriteProtectionService::OVERWRITE_PROTECTION_MODE;
        $columnNamesStr = ' ' . implode(', ', $columnNames);

        $newFieldsString = '--palette--;LLL:EXT:aoe_dbsequenzer/Resources/Private/Language/locallang_db.xml:protectoverwrite_headline;tx_aoe_dbsequenzer';
        $newFieldsStringRegex = preg_quote($newFieldsString, '/');

        foreach ($this->getTcaTablesWithOverwriteProtectionSupport() as $table) {
            // add columnsConfig at END of TCA-configuration
            ExtensionManagementUtility::addTCAcolumns($table, $additionalColumns);

            ExtensionManagementUtility::addFieldsToPalette(
                $table,
                'tx_aoe_dbsequenzer',
                $columnNamesStr
            );

            ExtensionManagementUtility::addToAllTCAtypes(
                $table,
                $newFieldsString
            );

            // move columnsConfig from END of TCA-configuration to BEGIN of TCA-configuration
            if (is_array($GLOBALS['TCA'][$table]['types'])) {
                foreach ($GLOBALS['TCA'][$table]['types'] as &$tableTypeConfig) {
                    if (array_key_exists('showitem', $tableTypeConfig) &&
                        preg_match('/' . $newFieldsStringRegex . '$/i', $tableTypeConfig['showitem'])) {

                        $showItems = &$tableTypeConfig['showitem'];

                        // 1. delete columnsConfig at END of TCA-configuration
                        $showItems = preg_replace('/,\s?' . $newFieldsStringRegex . '/i', '', $showItems);

                        // 2. add columnsConfig at BEGIN of TCA-configuration
                        if (preg_match('/^--div--/i', $showItems)) {
                            // first entry is an tab
                            $firstColumnEntry = substr($showItems, 0, stripos($showItems, ',') + 1);
                            $showItems = str_replace($firstColumnEntry, '', $showItems);
                            $showItems = $firstColumnEntry . $newFieldsString . ',' . $showItems;
                        } else {
                            // first entry is no tab
                            $showItems = $newFieldsString . ',' . $showItems;
                        }
                    }
                }
            }
        }

        $tca = $GLOBALS['TCA'];
        return [$tca];
    }

    /**
     * @return array
     */
    private function getTcaTablesWithOverwriteProtectionSupport()
    {
        $config = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['aoe_dbsequenzer']);
        if (isset($config['tables'])) {
            return explode(',', $config['tables']);
        }
        return [];
    }
}
