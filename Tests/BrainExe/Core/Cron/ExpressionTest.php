<?php

namespace BrainExe\Tests\Core\Cron;

use BrainExe\Core\Cron\Expression;
use PHPUnit_Framework_TestCase as TestCase;

class ExpressionTest extends TestCase
{

    /**
     * @var Expression
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Expression();
    }

    /**
     * @param string $expression
     * @param int $expected
     * @dataProvider provideData
     */
    public function testNextRun($expression, $expected)
    {
        $reference = "@1462702977";

        $actual = $this->subject->getNextRun($expression, $reference);

        $this->assertEquals($expected, $actual);
    }

    public function provideData()
    {
        return [
            ['@daily',      1462752000],
            ['* * * * *',   1462702980],
            ['*/5 * * * *', 1462703100],
        ];
    }
}
