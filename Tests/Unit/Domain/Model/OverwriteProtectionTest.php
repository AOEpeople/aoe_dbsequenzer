<?php
namespace Aoe\AoeDbSequenzer\Tests\Unit\Domain\Model;

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
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * @package Aoe\AoeDbSequenzer\Tests\Unit\Domain\Model
 * @covers \Aoe\AoeDbSequenzer\Domain\Model\OverwriteProtection
 */
class OverwriteProtectionTest extends UnitTestCase
{
    /**
     * @var OverwriteProtection
     */
    protected $overwriteProtection = null;

    /**
     * Set up creates a test instance and database.
     *
     * This method should be called with parent::setUp() in your test cases!
     *
     * @return void
     */
    public function setUp()
    {
        $this->overwriteProtection = new OverwriteProtection();
    }

    /**
     * @test
     */
    public function testDeleted()
    {
        $this->overwriteProtection->setDeleted(1);
        $this->assertEquals(1, $this->overwriteProtection->getDeleted());
    }

    /**
     * @test
     */
    public function testProtectedUid()
    {
        $this->overwriteProtection->setProtectedUid(1);
        $this->assertEquals(1, $this->overwriteProtection->getProtectedUid());
    }

    /**
     * @test
     */
    public function testProtectedTablename()
    {
        $this->overwriteProtection->setProtectedTablename('testTable');
        $this->assertEquals('testTable', $this->overwriteProtection->getProtectedTablename());
    }

    /**
     * @test
     */
    public function testProtectedTime()
    {
        $this->overwriteProtection->setProtectedTime(1234567);
        $this->assertEquals(1234567, $this->overwriteProtection->getProtectedTime());
    }

    /**
     * @test
     */
    public function testProtectedMode()
    {
        $this->overwriteProtection->setProtectedMode(10);
        $this->assertEquals(10, $this->overwriteProtection->getProtectedMode());
    }
}
