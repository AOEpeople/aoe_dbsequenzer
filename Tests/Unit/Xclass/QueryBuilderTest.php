<?php
namespace Aoe\AoeDbSequenzer\Tests\Unit\Xclass;


use Aoe\AoeDbSequenzer\Xclass\QueryBuilder;
use Nimut\TestingFramework\TestCase\UnitTestCase;

class QueryBuilderTest extends UnitTestCase
{
    /**
     * @var QueryBuilder
     */
    protected $subject;

    public function setUp(): void
    {
        $this->subject = $this->getAccessibleMock(QueryBuilder::class, ['dummy'], [], '', false);
    }

    /**
     * @test
     * @dataProvider sanitizeTableNameDataProvider
     */
    public function sanitizeTableName($tableName, $expected)
    {
        $this->assertEquals(
            $expected,
            $this->subject->_call('sanitizeTableName', $tableName)
        );
    }

    /**
     * @return array
     */
    public function sanitizeTableNameDataProvider()
    {
        return [
            'tableName with out quotes' => [
                'tableName' => 'sys_log',
                'expected' => 'sys_log'
            ],
            'tableName with back quotes' => [
                'tableName' => '`sys_log`',
                'expected' => 'sys_log'
            ],
            'empty tableName' => [
                'tableName' => '',
                'expected' => ''
            ]
        ];
    }


}
