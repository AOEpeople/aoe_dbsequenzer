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

use Aoe\AoeDbSequenzer\Xclass\QueryBuilder;


use Doctrine\DBAL\Driver\PDOSqlite\Driver;
use InvalidArgumentException;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class QueryBuilderTest extends FunctionalTestCase
{
    /**
     * @var QueryBuilder
     */
    protected $subject;

    public function setUp(): void
    {
        parent::setUp();
        $params = [];
        $driver = new Driver();
        $connection = GeneralUtility::makeInstance(Connection::class, $params, $driver);

        $this->subject = $this->getAccessibleMock(QueryBuilder::class, ['shouldTableBeSequenced'], [$connection], '', false);
        $this->subject->expects($this->once())->method('shouldTableBeSequenced')->willReturn(true);
    }

    /**
     * @test
     */
    public function QueryBuilderSetThrowsExceptionWhenUidIsKey()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(1564122229);

        $this->subject->set('uid', 124, true);
    }
}
