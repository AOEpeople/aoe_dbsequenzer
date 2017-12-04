<?php
namespace Aoe\AoeDbSequenzer\Tests\Unit\Service;

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

use Aoe\AoeDbSequenzer\Domain\Model\OverwriteProtection;
use Aoe\AoeDbSequenzer\Domain\Repository\OverwriteProtectionRepository;
use Aoe\AoeDbSequenzer\Service\OverwriteProtectionService;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Lang\LanguageService;

/**
 * @package Aoe\AoeDbSequenzer\Tests\Unit
 * @covers \Aoe\AoeDbSequenzer\OverwriteProtectionService
 */
class OverwriteProtectionServiceTest extends UnitTestCase
{
    /**
     * @var OverwriteProtectionService
     */
    private $overwriteProtectionService;

    /**
     * @var OverwriteProtectionRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $overwriteProtectionRepository;

    /**
     * @var PersistenceManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $persistenceManager;

    /**
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    public function setUp()
    {
        $testConfiguration['tables'] = 'table1,table2';
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['aoe_dbsequenzer'] = serialize($testConfiguration);

        $GLOBALS['TCA']['table1']['columns'][OverwriteProtectionService::OVERWRITE_PROTECTION_TILL]['config']['eval'] = 'datetime';

        $GLOBALS['BE_USER'] = $this->getMockBuilder(BackendUserAuthentication::class)
            ->disableOriginalConstructor()->getMock();
        $GLOBALS['BE_USER']->user = ['uid' => uniqid()];
        $GLOBALS['TYPO3_DB'] = $this->getMockBuilder(DatabaseConnection::class)
            ->disableOriginalConstructor()->getMock();
        $GLOBALS['LANG'] = $this->getMockBuilder(LanguageService::class)
            ->disableOriginalConstructor()->getMock();

        $this->persistenceManager = $this->getMockBuilder(PersistenceManager::class)
            ->setMethods(['persistAll'])->getMock();

        $this->overwriteProtectionRepository = $this->getMockBuilder(OverwriteProtectionRepository::class)
            ->setMethods(['findByProtectedUidAndTableName', 'add', 'update', 'remove'])
            ->getMock();

        /** @var ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject $objectManagerMock */
        $objectManagerMock = $this->getMockBuilder(ObjectManager::class)
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();
        $objectManagerMock->expects($this->any())
            ->method('get')
            ->willReturnMap([
                [OverwriteProtectionRepository::class, $this->overwriteProtectionRepository],
                [PersistenceManager::class, $this->persistenceManager],
                [OverwriteProtection::class, new OverwriteProtection()]
            ]);

        GeneralUtility::setSingletonInstance(ObjectManager::class, $objectManagerMock);

        $this->overwriteProtectionService = new OverwriteProtectionService();
    }

    /**
     * @test
     */
    public function processDatamap_preProcessFieldArray_NoOverwriteProtection_NotSupportedTable()
    {
        $incomingFieldArray = ['field1' => 'a'];
        /** @var DataHandler|\PHPUnit_Framework_MockObject_MockObject $dataHandlerMock */
        $dataHandlerMock = $this->getMockBuilder(DataHandler::class)->disableOriginalConstructor()->getMock();
        $this->overwriteProtectionService->processDatamap_preProcessFieldArray($incomingFieldArray, 'tableXY', 1, $dataHandlerMock);
        $this->assertFalse(isset($test[OverwriteProtectionService::OVERWRITE_PROTECTION_TILL]));
    }

    /**
     * @test
     */
    public function processDatamap_preProcessFieldArray_NoOverwriteProtection_MandatoryFieldsAreMissing()
    {
        $incomingFieldArray = ['field1' => 'a'];
        /** @var DataHandler|\PHPUnit_Framework_MockObject_MockObject $dataHandlerMock */
        $dataHandlerMock = $this->getMockBuilder(DataHandler::class)->disableOriginalConstructor()->getMock();
        $this->overwriteProtectionService->processDatamap_preProcessFieldArray($incomingFieldArray, 'table1', 1, $dataHandlerMock);
        $this->assertFalse(isset($test[OverwriteProtectionService::OVERWRITE_PROTECTION_TILL]));
    }

    /**
     * @test
     */
    public function processDatamap_preProcessFieldArray_RemoveExistingOverwriteProtection()
    {
        $incomingFieldArray = [
            OverwriteProtectionService::OVERWRITE_PROTECTION_TILL => '',
            OverwriteProtectionService::OVERWRITE_PROTECTION_MODE => '1'
        ];

        /** @var QueryResultInterface|\PHPUnit_Framework_MockObject_MockObject $mockQueryResult */
        $mockQueryResult = $this->getMock(QueryResultInterface::class);
        $mockQueryResult->expects($this->once())->method('toArray')->willReturn([
            new OverwriteProtection()
        ]);

        $this->overwriteProtectionRepository->expects($this->once())->method('findByProtectedUidAndTableName')
            ->willReturn($mockQueryResult);
        $this->overwriteProtectionRepository->expects($this->once())->method('remove');
        $this->persistenceManager->expects($this->once())->method('persistAll');

        /** @var DataHandler|\PHPUnit_Framework_MockObject_MockObject $dataHandlerMock */
        $dataHandlerMock = $this->getMockBuilder(DataHandler::class)->disableOriginalConstructor()->getMock();
        $this->overwriteProtectionService->processDatamap_preProcessFieldArray($incomingFieldArray, 'table1', 1, $dataHandlerMock);
        $this->assertFalse(isset($incomingFieldArray[OverwriteProtectionService::OVERWRITE_PROTECTION_TILL]));
        $this->assertFalse(isset($incomingFieldArray[OverwriteProtectionService::OVERWRITE_PROTECTION_MODE]));
    }

    /**
     * @test
     */
    public function processDatamap_preProcessFieldArray_WithProtection_AddNewOverwriteProtectionDataset()
    {
        /** @var QueryResultInterface|\PHPUnit_Framework_MockObject_MockObject $mockQueryResult */
        $mockQueryResult = $this->getMock(QueryResultInterface::class);
        $mockQueryResult->expects($this->once())->method('count')->willReturn(0);

        $this->overwriteProtectionRepository->expects($this->once())->method('findByProtectedUidAndTableName')
            ->willReturn($mockQueryResult);
        $this->overwriteProtectionRepository->expects($this->once())->method('add');
        $this->persistenceManager->expects($this->once())->method('persistAll');

        $incomingFieldArray = [
            OverwriteProtectionService::OVERWRITE_PROTECTION_TILL => '1323',
            OverwriteProtectionService::OVERWRITE_PROTECTION_MODE => '1'
        ];
        /** @var DataHandler|\PHPUnit_Framework_MockObject_MockObject $dataHandlerMock */
        $dataHandlerMock = $this->getMockBuilder(DataHandler::class)->disableOriginalConstructor()->getMock();
        $dataHandlerMock->expects($this->once())->method('checkValue_input_Eval')->willReturn(['value' => '1515625200']);

        $this->overwriteProtectionService->processDatamap_preProcessFieldArray($incomingFieldArray, 'table1', 1, $dataHandlerMock);
        $this->assertFalse(isset($incomingFieldArray[OverwriteProtectionService::OVERWRITE_PROTECTION_TILL]));
        $this->assertFalse(isset($incomingFieldArray[OverwriteProtectionService::OVERWRITE_PROTECTION_MODE]));
    }

    /**
     * @test
     */
    public function processDatamap_preProcessFieldArray_WithProtection_UpdateExistingOverwriteProtectionDataset()
    {
        /** @var QueryResultInterface|\PHPUnit_Framework_MockObject_MockObject $mockQueryResult */
        $mockQueryResult = $this->getMock(QueryResultInterface::class);
        $mockQueryResult->expects($this->once())->method('count')->willReturn(1);
        $mockQueryResult->expects($this->once())->method('toArray')->willReturn([
            new OverwriteProtection()
        ]);

        $this->overwriteProtectionRepository->expects($this->once())->method('findByProtectedUidAndTableName')
            ->willReturn($mockQueryResult);
        $this->overwriteProtectionRepository->expects($this->once())->method('update');
        $this->persistenceManager->expects($this->once())->method('persistAll');

        $incomingFieldArray = [
            OverwriteProtectionService::OVERWRITE_PROTECTION_TILL => '1323',
            OverwriteProtectionService::OVERWRITE_PROTECTION_MODE => '1'
        ];
        /** @var DataHandler|\PHPUnit_Framework_MockObject_MockObject $dataHandlerMock */
        $dataHandlerMock = $this->getMockBuilder(DataHandler::class)->disableOriginalConstructor()->getMock();
        $dataHandlerMock->expects($this->once())->method('checkValue_input_Eval')->willReturn(['value' => '1515625200']);

        $this->overwriteProtectionService->processDatamap_preProcessFieldArray($incomingFieldArray, 'table1', 1, $dataHandlerMock);
        $this->assertFalse(isset($incomingFieldArray[OverwriteProtectionService::OVERWRITE_PROTECTION_TILL]));
        $this->assertFalse(isset($incomingFieldArray[OverwriteProtectionService::OVERWRITE_PROTECTION_MODE]));
    }

    /**
     * @test
     */
    public function processCmdmap_postProcess_NotSupportedTable()
    {
        $this->overwriteProtectionRepository->expects($this->never())->method('findByProtectedUidAndTableName');
        $this->overwriteProtectionService->processCmdmap_postProcess('test', 'test', 1);
    }

    /**
     * @test
     */
    public function processCmdmap_postProcess_WithSupportedTableAndInvalidCommand()
    {
        $this->overwriteProtectionRepository->expects($this->never())->method('findByProtectedUidAndTableName');
        $this->overwriteProtectionService->processCmdmap_postProcess('test', 'table1', 1);
    }

    /**
     * @test
     */
    public function processCmdmap_postProcess_WithSupportedTableAndValidCommand()
    {
        /** @var QueryResultInterface|\PHPUnit_Framework_MockObject_MockObject $mockQueryResult */
        $mockQueryResult = $this->getMock(QueryResultInterface::class);
        $mockQueryResult->expects($this->once())->method('toArray')->willReturn([
            new OverwriteProtection()
        ]);

        $this->overwriteProtectionRepository->expects($this->once())->method('findByProtectedUidAndTableName')
            ->willReturn($mockQueryResult);
        $this->overwriteProtectionRepository->expects($this->once())->method('remove');
        $this->persistenceManager->expects($this->once())->method('persistAll');

        $this->overwriteProtectionService->processCmdmap_postProcess('delete', 'table1', 1);
    }

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    protected function tearDown()
    {
        unset($this->overwriteProtectionService);
        unset($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['aoe_dbsequenzer']);
        unset($GLOBALS['TCA']['table1']['columns'][OverwriteProtectionService::OVERWRITE_PROTECTION_TILL]['config']['eval']);
    }
}
