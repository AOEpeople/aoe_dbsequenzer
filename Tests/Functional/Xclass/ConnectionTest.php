<?php
declare(strict_types=1);
namespace Aoe\AoeDbSequenzer\Tests\Functional\Xclass;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2021 AOE GmbH <dev@aoe.com>
 *  All rights reserved
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Aoe\AoeDbSequenzer\Service\Typo3Service;
use Aoe\AoeDbSequenzer\Xclass\Connection;
use Doctrine\DBAL\Driver\PDOMySql\Driver;
use InvalidArgumentException;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConnectionTest extends FunctionalTestCase
{
    /**
     * @var Connection
     */
    protected $subject;

    public function setUp(): void
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
     */
    public function updateThrowsExceptionWhenUidInFieldArray()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(1564122222);

        $tableName = 'pages';
        $data = ['uid' => 123];
        $identifier = ['identifier' => 'abcd'];

        $this->subject->update($tableName, $data, $identifier);
    }
}
