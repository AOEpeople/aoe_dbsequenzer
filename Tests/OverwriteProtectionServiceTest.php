<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2009 AOE GmbH <dev@aoe.com>
 * All rights reserved
 *
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * test case for Tx_AoeDbsequenzer_OverwriteProtection
 * @package aoe_dbsequenzer
 * @subpackage Tests
 */
class Tx_AoeDbsequenzer_OverwriteProtectionServiceTest extends Tx_AoeDbsequenzer_BaseTest {
	/**
	 * @var Tx_AoeDbsequenzer_OverwriteProtectionService
	 */
	private $overwriteProtection;
	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() {
		$conf = array ();
		$conf ['tables'] = 'table1,table2';
		$this->overwriteProtection = new Tx_AoeDbsequenzer_OverwriteProtectionService ( $conf );
	}
	/**
	 * @test
	 */
	public function processDatamap_preProcessFieldArray() {
		$test = array ('field1' => 'a' );
		$this->overwriteProtection->processDatamap_preProcessFieldArray ( $test, 'table1', 1, $this->getMock ( 't3lib_TCEmain' ) );
		$this->assertFalse ( isset ( $test [Tx_AoeDbsequenzer_OverwriteProtectionService::OVERWRITE_PROTECTION_TILL] ) );
	}
	/**
	 * @test
	 */
	public function processDatamap_preProcessFieldArrayWithProtection() {
		$test = array ('field1' => 'a', Tx_AoeDbsequenzer_OverwriteProtectionService::OVERWRITE_PROTECTION_TILL => '1323' );
		$test = $this->overwriteProtection->processDatamap_preProcessFieldArray ( $test, 'table1', 1, $this->getMock ( 't3lib_TCEmain' ) );
		$this->assertFalse ( isset ( $test [Tx_AoeDbsequenzer_OverwriteProtectionService::OVERWRITE_PROTECTION_TILL] ) );
	}
	/**
	 * @test
	 */
	public function processCmdmap_postProcessWithoutValidTable() {
		$overwriteprotectionRepository = $this->getMock('Tx_AoeDbsequenzer_Domain_Repository_OverwriteprotectionRepository');
		$overwriteprotectionRepository->expects ( $this->never () )->method ( 'findByProtectedUidAndTableName' );
		$this->overwriteProtection->setOverwriteprotectionRepository($overwriteprotectionRepository);
		$this->overwriteProtection->processCmdmap_postProcess ( 'test', 'test', 1 );
	}
	/**
	 * @test
	 */
	public function processCmdmap_postProcessWithValidTableAndInvalidCommand() {
		$overwriteprotectionRepository = $this->getMock('Tx_AoeDbsequenzer_Domain_Repository_OverwriteprotectionRepository');
		$overwriteprotectionRepository->expects ( $this->never () )->method ( 'findByProtectedUidAndTableName' );
		$this->overwriteProtection->setOverwriteprotectionRepository($overwriteprotectionRepository);
		$this->overwriteProtection->processCmdmap_postProcess ( 'test', 'table1', 1 );
	}
	/**
	 * @test
	 */
	public function processCmdmap_postProcessWithValidTableAndValidCommand() {
		$overwriteprotectionRepository = $this->getMock('Tx_AoeDbsequenzer_Domain_Repository_OverwriteprotectionRepository');
		$overwriteprotectionRepository->expects ( $this->once () )->method ( 'findByProtectedUidAndTableName' )->will($this->returnValue(array()));
		$this->overwriteProtection->setOverwriteprotectionRepository($overwriteprotectionRepository);
		$this->overwriteProtection->processCmdmap_postProcess ( 'delete', 'table1', 1 );
	}
	/**
	 * @test
	 */
	public function renderInput() {
		$overwriteprotectionRepository = $this->getMock('Tx_AoeDbsequenzer_Domain_Repository_OverwriteprotectionRepository');
		$overwriteprotectionRepository->expects ( $this->once () )->method ( 'findByProtectedUidAndTableName' )->will($this->returnValue(array()));
		$this->overwriteProtection->setOverwriteprotectionRepository($overwriteprotectionRepository);
		$PA = array();
		$result = $this->overwriteProtection->renderInput ( $PA, $this->getMock('t3lib_TCEforms') );
		$this->assertNotNull($result);
		$this->assertNotContains('###UID###', $result);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown() {
		unset ( $this->overwriteProtection );
	}
}