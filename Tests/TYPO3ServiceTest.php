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
 * test case for Tx_AoeDbsequenzer_TYPO3Service
 * @package aoe_dbsequenzer
 * @subpackage Tests
 */
class Tx_AoeDbsequenzer_TYPO3ServiceTest extends Tx_AoeDbsequenzer_BaseTest {
	/**
	 * @var Tx_AoeDbsequenzer_TYPO3Service
	 */
	private $service;
	/**
	 * @var PHPUnit_Framework_MockObject_MockObject
	 */
	private $sequenzer;
	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() {
		$conf = array ();
		$conf ['offset'] = '1';
		$conf ['system'] = 'testa';
		$conf ['tables'] = 'table1,table2';
		$this->sequenzer = $this->getMock ( 'Tx_AoeDbsequenzer_Sequenzer', array (), array (), '', FALSE );
		$this->service = new Tx_AoeDbsequenzer_TYPO3Service ( $this->sequenzer, $conf );
	}
	/**
	 * @test
	 */
	public function modifyInsertFields() {
		$this->sequenzer->expects ( $this->once () )->method ( 'getNextIdForTable' )->will ( $this->returnValue ( 1 ) );
		$test = $this->service->modifyInsertFields ( 'table1', array ('field1' => 'a' ) );
		$this->assertTrue ( isset ( $test ['uid'] ) );
	}
	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown() {
		unset ( $this->sequenzer );
		unset ( $this->service );
	}
}