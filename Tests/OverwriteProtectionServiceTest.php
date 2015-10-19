<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 AOE GmbH (dev@aoe.com)
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
	public function setUp() {
		$conf = array ();
		$conf ['tables'] = 'table1,table2';

		$GLOBALS['BE_USER'] = $this->getMock('TYPO3\\CMS\\Core\\Authentication\\BackendUserAuthentication', array(), array(), '', FALSE);
		$GLOBALS['BE_USER']->user = array('uid' => uniqid());
		$GLOBALS['TYPO3_DB'] = $this->getMock('TYPO3\\CMS\\Core\\Database\\DatabaseConnection', array(), array(), '', FALSE);
		$GLOBALS['LANG'] = $this->getMock('TYPO3\\CMS\\Lang\\LanguageService', array(), array(), '', FALSE);

		$objectManagerMock = $this->getMockBuilder('TYPO3\CMS\Extbase\Object\ObjectManager')
				->getMock();
		$objectManagerMock->method('get')->willReturn($this->getMock('Tx_Extbase_Persistence_Manager', array('persistAll')));
		$this->overwriteProtection = new Tx_AoeDbsequenzer_OverwriteProtectionService ( $conf );
		$this->overwriteProtection->injectObjectManager($objectManagerMock);
	}
	/**
	 * @test
	 */
	public function processDatamap_preProcessFieldArray() {
		$test = array ('field1' => 'a' );
		$this->overwriteProtection->processDatamap_preProcessFieldArray ( $test, 'table1', 1, $this->getMock ( 'TYPO3\\CMS\\Core\\DataHandling\\DataHandler' ) );
		$this->assertFalse ( isset ( $test [Tx_AoeDbsequenzer_OverwriteProtectionService::OVERWRITE_PROTECTION_TILL] ) );
	}
	/**
	 * @test
	 */
	public function processDatamap_preProcessFieldArrayWithProtection() {
		$test = array ('field1' => 'a', Tx_AoeDbsequenzer_OverwriteProtectionService::OVERWRITE_PROTECTION_TILL => '1323' );
		$test = $this->overwriteProtection->processDatamap_preProcessFieldArray ( $test, 'table1', 1, $this->getMock ( 'TYPO3\\CMS\\Core\\DataHandling\\DataHandler' ) );
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
		$formEngineMock = $this->getMock('\TYPO3\CMS\Backend\Form\FormEngine');
		$result = $this->overwriteProtection->renderInput ( $PA, $formEngineMock );
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