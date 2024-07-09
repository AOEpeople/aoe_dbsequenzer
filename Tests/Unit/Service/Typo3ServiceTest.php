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

use Aoe\AoeDbSequenzer\Sequenzer;
use Aoe\AoeDbSequenzer\Service\Typo3Service;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\ApplicationContext;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @package Aoe\AoeDbSequenzer\Tests\Unit
 * @covers \Aoe\AoeDbSequenzer\Service\Typo3Service
 */
class Typo3ServiceTest extends UnitTestCase
{
    private Typo3Service $service;

    /**
     * @var Sequenzer|MockObject
     */
    private MockObject $sequenzer;

    protected bool $backupEnvironment = true;

    protected function setUp(): void
    {
        parent::setUp();

        Environment::initialize(
            new ApplicationContext('Testing'),
            Environment::isCli(),
            Environment::isComposerMode(),
            Environment::getProjectPath(),
            Environment::getPublicPath(),
            Environment::getVarPath(),
            __DIR__ . '/../../../.Build/Web/config',
            Environment::getCurrentScript(),
            'UNIX',
        );

        $testConfiguration = [];
        $testConfiguration['aoe_dbsequenzer']['offset'] = '1';
        $testConfiguration['aoe_dbsequenzer']['system'] = 'testa';
        $testConfiguration['aoe_dbsequenzer']['tables'] = 'table1,table2';
        GeneralUtility::makeInstance(ExtensionConfiguration::class)->setAll($testConfiguration);

        $this->sequenzer = $this->getMockBuilder(Sequenzer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->service = new Typo3Service($this->sequenzer);
    }

    public function testModifyInsertFields_NotSupportedTable(): void
    {
        $this->resetSingletonInstances = true;
        $this->sequenzer->expects($this->never())->method('getNextIdForTable');
        $modifiedFields = $this->service->modifyInsertFields('tableXY', ['field1' => 'a']);
        $this->assertArrayNotHasKey('uid', $modifiedFields);
    }

    public function testModifyInsertFields_GetNextIdForTable(): void
    {
        $this->resetSingletonInstances = true;
        $this->sequenzer->expects($this->once())->method('getNextIdForTable')->willReturn(1);
        $modifiedFields = $this->service->modifyInsertFields('table1', ['field1' => 'a']);
        $this->assertArrayHasKey('uid', $modifiedFields);
        $this->assertSame(1, $modifiedFields['uid']);
    }
}
