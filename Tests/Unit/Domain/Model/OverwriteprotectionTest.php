<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 AOE GmbH <dev@aoe.com>
 *  All rights reserved
 *
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Class OverwriteprotectionTest
 */
class OverwriteprotectionTest extends Tx_AoeDbsequenzer_BaseTest
{
    /**
     * @var Tx_AoeDbsequenzer_Domain_Model_Overwriteprotection
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
        $this->_overwriteProtectionObject = new Tx_AoeDbsequenzer_Domain_Model_Overwriteprotection();
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
