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
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * @package Aoe\AoeDbSequenzer\Tests\Unit
 * @covers \Aoe\AoeDbSequenzer\Service\Typo3Service
 */
class Typo3ServiceTest extends UnitTestCase
{
    /**
     * @var Typo3Service
     */
    private $service;

    /**
     * @var Sequenzer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sequenzer;

    /**
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    public function setUp()
    {
        $testConfiguration = [];
        $testConfiguration['offset'] = '1';
        $testConfiguration['system'] = 'testa';
        $testConfiguration['tables'] = 'table1,table2';
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['aoe_dbsequenzer'] = serialize($testConfiguration);

        $this->sequenzer = $this->getMockBuilder(Sequenzer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->service = new Typo3Service($this->sequenzer);
    }

    /**
     * @test
     */
    public function modifyInsertFields_NotSupportedTable()
    {
        $this->sequenzer->expects($this->never())->method('getNextIdForTable');
        $modifiedFields = $this->service->modifyInsertFields('tableXY', ['field1' => 'a']);
        $this->assertFalse(isset($modifiedFields['uid']));
    }

    /**
     * @test
     */
    public function modifyInsertFields_GetNextIdForTable()
    {
        $this->sequenzer->expects($this->once())->method('getNextIdForTable')->willReturn(1);
        $modifiedFields = $this->service->modifyInsertFields('table1', ['field1' => 'a']);
        $this->assertTrue(isset($modifiedFields['uid']));
        $this->assertEquals(1, $modifiedFields['uid']);
    }

    /**
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    protected function tearDown()
    {
        unset($this->sequenzer);
        unset($this->service);
        unset($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['aoe_dbsequenzer']);
    }
}
