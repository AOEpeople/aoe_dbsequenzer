<?php
namespace Aoe\AoeDbSequenzer\Tests\Unit;

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

use Aoe\AoeDbSequenzer\Domain\Repository\OverwriteProtectionRepository;
use Aoe\AoeDbSequenzer\OverwriteProtectionService;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Lang\LanguageService;

/**
 * @package Aoe\AoeDbSequenzer\Tests\Unit
 */
class OverwriteProtectionServiceTest extends UnitTestCase
{
    /**
     * @var OverwriteProtectionService
     */
    private $overwriteProtectionService;

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    public function setUp()
    {
        $conf = array();
        $conf ['tables'] = 'table1,table2';

        $GLOBALS['BE_USER'] = $this->getMock(BackendUserAuthentication::class, array(), array(), '', false);
        $GLOBALS['BE_USER']->user = array('uid' => uniqid());
        $GLOBALS['TYPO3_DB'] = $this->getMock(DatabaseConnection::class, array(), array(), '', false);
        $GLOBALS['LANG'] = $this->getMock(LanguageService::class, array(), array(), '', false);

        /** @var ObjectManager|\PHPUnit_Framework_MockObject_MockObject $objectManagerMock */
        $objectManagerMock = $this->getMockBuilder(ObjectManager::class)->getMock();
        $objectManagerMock->method('get')->willReturn($this->getMock(PersistenceManager::class, array('persistAll')));
        $this->overwriteProtectionService = new OverwriteProtectionService ($conf);
        $this->overwriteProtectionService->injectObjectManager($objectManagerMock);
    }

    /**
     * @test
     */
    public function processDatamap_preProcessFieldArray()
    {
        $test = array('field1' => 'a');
        /** @var DataHandler|\PHPUnit_Framework_MockObject_MockObject $dataHandlerMock */
        $dataHandlerMock = $this->getMockBuilder(DataHandler::class)->disableOriginalConstructor()->getMock();
        $this->overwriteProtectionService->processDatamap_preProcessFieldArray($test, 'table1', 1, $dataHandlerMock);
        $this->assertFalse(isset ($test [OverwriteProtectionService::OVERWRITE_PROTECTION_TILL]));
    }

    /**
     * @test
     */
    public function processDatamap_preProcessFieldArrayWithProtection()
    {
        $test = array('field1' => 'a', OverwriteProtectionService::OVERWRITE_PROTECTION_TILL => '1323');
        /** @var DataHandler|\PHPUnit_Framework_MockObject_MockObject $dataHandlerMock */
        $dataHandlerMock = $this->getMockBuilder(DataHandler::class)->disableOriginalConstructor()->getMock();
        $test = $this->overwriteProtectionService->processDatamap_preProcessFieldArray($test, 'table1', 1,
            $dataHandlerMock);
        $this->assertFalse(isset ($test [OverwriteProtectionService::OVERWRITE_PROTECTION_TILL]));
    }

    /**
     * @test
     */
    public function processCmdmap_postProcessWithoutValidTable()
    {
        /** @var OverwriteProtectionRepository|\PHPUnit_Framework_MockObject_MockObject $overwriteProtectionRepository */
        $overwriteProtectionRepository = $this->getMock(OverwriteProtectionRepository::class);
        $overwriteProtectionRepository->expects($this->never())->method('findByProtectedUidAndTableName');
        $this->overwriteProtectionService->setOverwriteprotectionRepository($overwriteProtectionRepository);
        $this->overwriteProtectionService->processCmdmap_postProcess('test', 'test', 1);
    }

    /**
     * @test
     */
    public function processCmdmap_postProcessWithValidTableAndInvalidCommand()
    {
        /** @var OverwriteProtectionRepository|\PHPUnit_Framework_MockObject_MockObject $overwriteProtectionRepository */
        $overwriteProtectionRepository = $this->getMock(OverwriteProtectionRepository::class);
        $overwriteProtectionRepository->expects($this->never())->method('findByProtectedUidAndTableName');
        $this->overwriteProtectionService->setOverwriteprotectionRepository($overwriteProtectionRepository);
        $this->overwriteProtectionService->processCmdmap_postProcess('test', 'table1', 1);
    }

    /**
     * @test
     */
    public function processCmdmap_postProcessWithValidTableAndValidCommand()
    {
        /** @var OverwriteProtectionRepository|\PHPUnit_Framework_MockObject_MockObject $overwriteProtectionRepository */
        $overwriteProtectionRepository = $this->getMock(OverwriteProtectionRepository::class);
        $overwriteProtectionRepository->expects($this->once())->method('findByProtectedUidAndTableName')->willReturn([]);
        $this->overwriteProtectionService->setOverwriteprotectionRepository($overwriteProtectionRepository);
        $this->overwriteProtectionService->processCmdmap_postProcess('delete', 'table1', 1);
    }

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    protected function tearDown()
    {
        unset ($this->overwriteProtectionService);
    }
}
