<?php

namespace Aoe\AoeDbSequenzer\Tests\Unit\Xclass;


use Aoe\AoeDbSequenzer\Xclass\QueryBuilder;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class QueryBuilderTest extends UnitTestCase
{
    /**
     * @var QueryBuilder
     */
    protected $subject;

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
            'tableName' => 'sys_log',
            'expected' => 'sys_log'
        ];
        yield 'tableName with back quotes' => [
            'tableName' => '`sys_log`',
            'expected' => 'sys_log'
        ];
        yield 'empty tableName' => [
            'tableName' => '',
            'expected' => ''
        ];
    }
}
