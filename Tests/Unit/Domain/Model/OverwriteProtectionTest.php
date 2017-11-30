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
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * @package Aoe\AoeDbSequenzer\Tests\Unit\Domain\Model
 */
class OverwriteProtectionTest extends UnitTestCase
{
    /**
     * @var OverwriteProtection
     */
    protected $_overwriteProtectionObject = null;

    /**
     * Set up creates a test instance and database.
     *
     * This method should be called with parent::setUp() in your test cases!
     *
     * @return void
     */
    public function setUp()
    {
        $this->_overwriteProtectionObject = new Overwriteprotection();
        parent::setUp();
    }

    /**
     * @test
     */
    public function testDeleted()
    {
        $this->_overwriteProtectionObject->setDeleted(1);
        $this->assertEquals(1, $this->_overwriteProtectionObject->getDeleted());
    }

    /**
     * @test
     */
    public function testProtectedUid()
    {
        $this->_overwriteProtectionObject->setProtectedUid(1);
        $this->assertEquals(1, $this->_overwriteProtectionObject->getProtectedUid());
    }

    /**
     * @test
     */
    public function testProtectedTablename()
    {
        $this->_overwriteProtectionObject->setProtectedTablename('testTable');
        $this->assertEquals('testTable', $this->_overwriteProtectionObject->getProtectedTablename());
    }

    /**
     * @test
     */
    public function testProtectedTime()
    {
        $this->_overwriteProtectionObject->setProtectedTime(1234567);
        $this->assertEquals(1234567, $this->_overwriteProtectionObject->getProtectedTime());
    }

    /**
     * @test
     */
    public function testProtectedMode()
    {
        $this->_overwriteProtectionObject->setProtectedMode(10);
        $this->assertEquals(10, $this->_overwriteProtectionObject->getProtectedMode());
    }
}