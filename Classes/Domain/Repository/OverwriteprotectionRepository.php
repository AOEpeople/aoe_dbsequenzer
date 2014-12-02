<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2011 AOE media GmbH <dev@aoemedia.de>
 * All rights reserved
 *
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
/**
 * Overwriteprotection Repository
 * @package aoe_dbsequenzer
 */
class Tx_AoeDbsequenzer_Domain_Repository_OverwriteprotectionRepository extends Tx_Extbase_Persistence_Repository {

    /**
     *
     */
    public function __construct()
    {
        $objectManager = t3lib_div::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
        parent::__construct($objectManager);
    }

	/**
	 * remove storage page
	 */
	public function initializeObject() {
		$querySettings = $this->objectManager->create ( 'Tx_Extbase_Persistence_Typo3QuerySettings' );
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