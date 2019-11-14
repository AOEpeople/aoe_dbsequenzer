<?php
namespace Aoe\AoeDbSequenzer\Form;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2019 AOE GmbH (dev@aoe.com)
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

use Aoe\AoeDbSequenzer\Domain\Model\OverwriteProtection;
use Aoe\AoeDbSequenzer\Domain\Repository\OverwriteProtectionRepository;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Lang\LanguageService;

/**
 * @package Aoe\AoeDbSequenzer\Form
 */
abstract class AbstractOverwriteElement
{
    /**
     * @param integer $protectedUid
     * @param string $tableName
     * @return boolean
     */
    protected function hasOverwriteProtection($protectedUid, $tableName)
    {
        if (false === is_numeric($protectedUid)) {
            return false;
        }

        $countOverwriteProtections = $this->getOverwriteProtectionRepository()
            ->findByProtectedUidAndTableName($protectedUid, $tableName)
            ->count();
        return ($countOverwriteProtections > 0);
    }

    /**
     * @param integer $protectedUid
     * @param string $tableName
     * @return OverwriteProtection
     */
    protected function getOverwriteProtection($protectedUid, $tableName)
    {
        return $this->getOverwriteProtectionRepository()
            ->findByProtectedUidAndTableName($protectedUid, $tableName)
            ->getFirst();
    }

    /**
     * @return LanguageService
     * @codeCoverageIgnore
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }

    /**
     * @return NodeFactory
     * @codeCoverageIgnore
     */
    protected function getNodeFactory()
    {
        return GeneralUtility::makeInstance(NodeFactory::class);
    }

    /**
     * @return OverwriteProtectionRepository
     * @codeCoverageIgnore
     */
    protected function getOverwriteProtectionRepository()
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        return $objectManager->get(OverwriteProtectionRepository::class);
    }
}
