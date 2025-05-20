<?php

namespace Aoe\AoeDbSequenzer\Tests\Unit\Xclass;

use Aoe\AoeDbSequenzer\Xclass\QueryBuilder;
use PHPUnit\Framework\Attributes\DataProvider;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class QueryBuilderTest extends UnitTestCase
{
    protected QueryBuilder $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->getAccessibleMock(QueryBuilder::class, null, [], '', false);
    }

    #[DataProvider('sanitizeTableNameDataProvider')]
    public function testSanitizeTableName(string $tableName, string $expected): void
    {
        $this->assertSame(
            $expected,
            $this->subject->_call('sanitizeTableName', $tableName)
        );
    }

    public static function sanitizeTableNameDataProvider(): \Iterator
    {
        yield 'tableName with out quotes' => [
            'sys_log',
            'sys_log',
        ];
        yield 'tableName with back quotes' => [
            '`sys_log`',
            'sys_log',
        ];
        yield 'empty tableName' => [
            '',
            '',
        ];
    }
}
