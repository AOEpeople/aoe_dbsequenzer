<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 AOE GmbH (dev@aoe.com)
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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

use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Lang\LanguageService;

/**
 * @package aoe_dbsequenzer
 */
abstract class Tx_AoeDbsequenzer_Form_AbstractOverwriteElement {

    /**
     * @param integer $protectedUid
     * @param string $tableName
     * @return boolean
     */
    protected function hasOverwriteProtection($protectedUid, $tableName)
    {
        $result = $this->getOverwriteprotectionRepository()->findByProtectedUidAndTableName($protectedUid, $tableName);
        return ($result->count() > 0);
    }

    /**
     * @param integer $protectedUid
     * @param string $tableName
     * @return Tx_AoeDbsequenzer_Domain_Model_Overwriteprotection
     */
    protected function getOverwriteProtection($protectedUid, $tableName)
    {
        $result = $this->getOverwriteprotectionRepository()->findByProtectedUidAndTableName($protectedUid, $tableName);
        foreach ($result as $overwriteprotection) {
            /* @var $overwriteprotection Tx_AoeDbsequenzer_Domain_Model_Overwriteprotection */
            return $overwriteprotection;
        }

        return null;
    }

    /**
     * @return LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }

    /**
     * @return NodeFactory
     */
    protected function getNodeFactory()
    {
        return GeneralUtility::makeInstance(NodeFactory::class);
    }

    /**
     * @return Tx_AoeDbsequenzer_Domain_Repository_OverwriteprotectionRepository
     */
    protected function getOverwriteprotectionRepository()
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        return $objectManager->get('Tx_AoeDbsequenzer_Domain_Repository_OverwriteprotectionRepository');
    }
}