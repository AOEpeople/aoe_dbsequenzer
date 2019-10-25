<?php
declare(strict_types=1);
namespace Aoe\AoeDbSequenzer\Tests\Functional\Xclass;

use Aoe\AoeDbSequenzer\Service\Typo3Service;
use Aoe\AoeDbSequenzer\Xclass\Connection;
use Doctrine\DBAL\Driver\PDOMySql\Driver;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2019 AOE GmbH <dev@aoe.com>
 *  All rights reserved
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/


class ConnectionTest extends FunctionalTestCase
{
    /**
     * @var Connection
     */
    protected $subject;

    protected $testExtensionsToLoad = ['typo3conf/ext/aoe_dbsequenzer'];
    protected $coreExtensionsToLoad = ['core', 'cms', 'extensionmanager'];

    public function setUp()
    {
        parent::setUp();
        $params = [];
        $driver = new Driver();

        $this->subject = GeneralUtility::makeInstance(Connection::class, $params, $driver);

        $typo3Service = $this->getMockBuilder(Typo3Service::class)
            ->disableOriginalConstructor()
            ->setMethods(['needsSequenzer'])
            ->getMock();

        $typo3Service->expects($this->any())->method('needsSequenzer')->willReturn($this->returnValue(true));

        $this->inject($this->subject, 'typo3Service', $typo3Service);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionCode 1564122222
     */
    public function updateThrowsExceptionWhenUidInFieldArray()
    {
        $tableName = 'pages';
        $data = ['uid' => 123];
        $identifier = ['identifier' => 'abcd'];

        $this->subject->update($tableName, $data, $identifier);
    }

}
