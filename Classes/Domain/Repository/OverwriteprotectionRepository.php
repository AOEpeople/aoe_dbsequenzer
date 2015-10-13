<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 AOE GmbH (dev@aoe.com)
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

use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Overwriteprotection Repository
 * @package aoe_dbsequenzer
 */
class Tx_AoeDbsequenzer_Domain_Repository_OverwriteprotectionRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

    /**
     *
     */
    public function __construct()
    {
        $objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        parent::__construct($objectManager);
    }

	/**
	 * remove storage page
	 */
	public function initializeObject() {
		$querySettings = $this->objectManager->get ( 'Tx_Extbase_Persistence_Typo3QuerySettings' );
		$querySettings->setRespectStoragePage ( FALSE );
		$this->setDefaultQuerySettings ( $querySettings );
	}
	/**
	 * @param integer $protectedUid
	 * @param string $tableName
	 * @return Tx_Extbase_Persistence_QueryResultInterface
	 */
	public function findByProtectedUidAndTableName($protectedUid,$tableName){
		$query = $this->createQuery();
		$query->matching ( $query->logicalAnd ( $query->equals ( 'protected_uid', intval($protectedUid) ), $query->equals ( 'protected_tablename', $tableName)) );
		return $query->execute ();
	}
}