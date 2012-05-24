<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2009 AOE media GmbH <dev@aoemedia.de>
 * All rights reserved
 *
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
/**
 * Overwriteprotection
 * @package aoe_survey
 */
class Tx_AoeDbsequenzer_Domain_Model_Overwriteprotection extends Tx_Extbase_DomainObject_AbstractEntity {
	/**
	 * @var integer
	 */
	protected $deleted;
	/**
	 * @var integer
	 */
	protected $protectedUid;
	/**
	 * @var string
	 */
	protected $protectedTablename;
	/**
	 * @var integer
	 */
	protected $protectedTime;
	/**
	 * @var integer
	 */
	protected $protectedMode;
	/**
	 * @return integer
	 */
	public function getProtectedUid() {
		return $this->protectedUid;
	}
	/**
	 * @return string
	 */
	public function getProtectedTablename() {
		return $this->protectedTablename;
	}
	/**
	 * @param integer $protectedUid
	 */
	public function setProtectedUid($protectedUid) {
		$this->protectedUid = intval($protectedUid);
	}
	/**
	 * @param string $protectedTablename
	 */
	public function setProtectedTablename($protectedTablename) {
		$this->protectedTablename = $protectedTablename;
	}
	/**
	 * @return integer
	 */
	public function getProtectedTime() {
		return $this->protectedTime;
	}
	/**
	 * @param integer $protectedTime
	 */
	public function setProtectedTime($protectedTime) {
		$this->protectedTime = intval($protectedTime);
	}
	/**
	 * @return integer
	 */
	public function getProtectedMode() {
		return $this->protectedMode;
	}

	/**
	 * @param integer $protectedMode
	 */
	public function setProtectedMode($protectedMode) {
		$this->protectedMode = intval($protectedMode);
	}
	/**
	 * @return integer
	 */
	public function getDeleted() {
		return $this->deleted;
	}

	/**
	 * @param integer $deleted
	 */
	public function setDeleted($deleted) {
		$this->deleted = $deleted;
	}


}