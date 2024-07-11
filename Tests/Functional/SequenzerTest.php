<?php

declare(strict_types=1);

namespace Aoe\AoeDbSequenzer\Tests\Functional;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2024 AOE GmbH <dev@aoe.com>
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
use Exception;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use ReflectionException;
use ReflectionMethod;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SequenzerTest extends FunctionalTestCase
{
    protected Sequenzer $subject;

    protected array $testExtensionsToLoad = ['typo3conf/ext/aoe_dbsequenzer'];

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new Sequenzer();
    }

    public function testGetNextIdForTable_throwsExceptionDepthToHigh(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionCode(1512378158);

        $this->subject->getNextIdForTable('tableName', 1000);
    }

    public function testGetNextIdForTable_NoPagesAdded(): void
    {
        $this->subject->setDefaultOffset(10);
        $this->subject->setDefaultStart(4);

        $this->assertSame(
            24,
            $this->subject->getNextIdForTable('pages')
        );
    }

    public function testGetNextIdForTable_OutDatedSequencerInformation(): void
    {
        // Offset is set in Fixture (20)
        // Current is set in Fixture (5)
        $this->importCSVDataSet(__DIR__ . '/Fixtures/tx_aoedbsequenzer_seqeunz.csv');
        $this->importCSVDataSet(__DIR__ . '/Fixtures/pages.csv');

        $this->assertSame(
            28,
            $this->subject->getNextIdForTable('pages')
        );
    }

    public function testGetDefaultStartValue_withoutOffsetConfigured(): void
    {
        // Imports 7 pages, therefor the expected StartValue should be 8
        $this->importCSVDataSet(__DIR__ . '/Fixtures/pages.csv');
        $method = $this->getPrivateMethod($this->subject, 'getDefaultStartValue');

        $result = $method->invokeArgs($this->subject, ['pages']);
        $this->assertSame(8, $result);
    }

    public function testGetDefaultStartValue_withOffsetConfigured(): void
    {
        $this->subject->setDefaultOffset(6);
        $this->importCSVDataSet(__DIR__ . '/Fixtures/pages.csv');
        $method = $this->getPrivateMethod($this->subject, 'getDefaultStartValue');
        $result = $method->invokeArgs($this->subject, ['pages']);

        // As the current largest uid is 7, and an offset of 6, the next free value would be 12
        $this->assertSame(12, $result);
    }

    public function testGetDefaultStartValue_withOffsetAndDefaultStartValueConfigured(): void
    {
        $this->subject->setDefaultOffset(14);
        $this->subject->setDefaultStart(20);
        $this->importCSVDataSet(__DIR__ . '/Fixtures/pages.csv');
        $method = $this->getPrivateMethod($this->subject, 'getDefaultStartValue');
        $result = $method->invokeArgs($this->subject, ['pages']);

        // As the current largest uid is 20, and an offset of 14, the next free value would be 34
        $this->assertSame(34, $result);
    }

    public function testInitSequenzerForTable_isDataInsert(): void
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable(Sequenzer::SEQUENZER_TABLE);

        // Before Init
        $this->assertSame(
            0,
            $queryBuilder->count('*', Sequenzer::SEQUENZER_TABLE, ['tablename' => 'pages'])
        );

        $method = $this->getPrivateMethod($this->subject, 'initSequenzerForTable');
        $method->invokeArgs($this->subject, ['pages']);

        // After Init
        $this->assertSame(
            1,
            $queryBuilder->count('*', Sequenzer::SEQUENZER_TABLE, ['tablename' => 'pages'])
        );
    }

    /**
     * @param $className
     * @param $methodName
     *
     * @return ReflectionMethod
     * @throws ReflectionException
     */
    public function getPrivateMethod($className, $methodName)
    {
        $reflector = new \ReflectionClass($className);
        $method = $reflector->getMethod($methodName);

        return $method;
    }
}
