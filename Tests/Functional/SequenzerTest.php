<?php
declare(strict_types=1);
namespace Aoe\AoeDbSequenzer\Tests\Functional;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2019 AOE GmbH <dev@aoe.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SequenzerTest extends FunctionalTestCase
{

    /**
     * @var Sequenzer
     */
    protected $subject;

    protected $testExtensionsToLoad = ['typo3conf/ext/aoe_dbsequenzer'];

    public function setUp()
    {
        parent::setUp();
        $this->subject = new Sequenzer();
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionCode 1512378158
     * @throws \Exception
     */
    public function getNextIdForTable_throwsExceptionDepthToHigh()
    {
        $this->subject->getNextIdForTable('tableName', 1000);
    }

    /**
     * @test
     */
    public function getDefaultStartValue_withoutOffsetConfigured()
    {
        // Imports 7 pages, therefor the expected StartValue should be 8
        $this->importDataSet('ntf://Database/pages.xml');
        $method = $this->getPrivateMethod($this->subject, 'getDefaultStartValue');

        $result = $method->invokeArgs($this->subject, ['pages']);
        $this->assertSame(8, $result);
    }

    /**
     * @test
     */
    public function getDefaultStartValue_withOffsetConfigured()
    {
        $this->subject->setDefaultOffset(6);
        $this->importDataSet('ntf://Database/pages.xml');
        $method = $this->getPrivateMethod($this->subject, 'getDefaultStartValue');
        $result = $method->invokeArgs($this->subject, ['pages']);

        // As the current largest uid is 7, and an offset of 6, the next free value would be 12
        $this->assertSame(12, $result);
    }

    /**
     * @test
     */
    public function getDefaultStartValue_withOffsetAndDefaultStartValueConfigured()
    {
        $this->subject->setDefaultOffset(14);
        $this->subject->setDefaultStart(20);
        $this->importDataSet('ntf://Database/pages.xml');
        $method = $this->getPrivateMethod($this->subject, 'getDefaultStartValue');
        $result = $method->invokeArgs($this->subject, ['pages']);

        // As the current largest uid is 20, and an offset of 14, the next free value would be 34
        $this->assertSame(34, $result);
    }

    /**
     * @test
     */
    public function initSequenzerForTable_isDataInsert()
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable(Sequenzer::SEQUENZER_TABLE);

        // Before Init
        $this->assertEquals(
            0,
            $queryBuilder->count('*', Sequenzer::SEQUENZER_TABLE, ['tablename' => 'pages'])
        );

        $method = $this->getPrivateMethod($this->subject, 'initSequenzerForTable');
        $method->invokeArgs($this->subject, ['pages']);

        // After Init
        $this->assertEquals(
            1,
            $queryBuilder->count('*', Sequenzer::SEQUENZER_TABLE, ['tablename' => 'pages'])
        );
    }

    /**
     * @param $className
     * @param $methodName
     * @return \ReflectionMethod
     * @throws \ReflectionException
     */
    public function getPrivateMethod($className, $methodName)
    {
        $reflector = new \ReflectionClass($className);
        $method = $reflector->getMethod($methodName);
        $method->setAccessible(true);

        return $method;
    }
}
