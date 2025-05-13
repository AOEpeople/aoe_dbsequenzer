<?php
declare(strict_types=1);
namespace Aoe\AoeDbSequenzer\Tests\Functional\Xclass;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2024 AOE GmbH <dev@aoe.com>
 *  All rights reserved
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Aoe\AoeDbSequenzer\Service\Typo3Service;
use Aoe\AoeDbSequenzer\Xclass\Connection;
use Doctrine\DBAL\Driver\Mysqli\Driver;
use InvalidArgumentException;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConnectionTest extends FunctionalTestCase
{
    /**
     * @var Connection
     */
    protected $subject;

    protected function setUp(): void
    {

        if (VersionNumberUtility::convertVersionNumberToInteger(VersionNumberUtility::getCurrentTypo3Version()) < 12000000)
        {
            restore_error_handler();
        }

        parent::setUp();
        $params = [];
        $driver = new Driver();

        $this->subject = GeneralUtility::makeInstance(Connection::class, $params, $driver);

        $typo3Service = $this->getMockBuilder(Typo3Service::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['needsSequenzer'])
            ->getMock();

        $typo3Service->method('needsSequenzer')->willReturn(true);

        GeneralUtility::setSingletonInstance(Typo3Service::class, $typo3Service);
    }

    public function testUpdateThrowsExceptionWhenUidInFieldArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(1564122222);

        $tableName = 'pages';
        $data = ['uid' => 123];
        $identifier = ['identifier' => 'abcd'];

        $this->subject->update($tableName, $data, $identifier);
    }
}
