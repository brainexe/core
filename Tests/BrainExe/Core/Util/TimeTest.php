<?php

namespace Tests\BrainExe\Core\Util;

use BrainExe\Core\Util\Time;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers \BrainExe\Core\Util\Time
 */
class TimeTest extends TestCase
{

    /**
     * @var Time
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Time();
    }

    public function testNow()
    {
        $actual = $this->subject->now();

        $this->assertEquals(time(), $actual, "current time", 1);
    }

    public function testDate()
    {
        $actual = $this->subject->date('y');
        $expected = date('y');

        $this->assertEquals($expected, $actual);
    }

    public function testMicrotime()
    {
        $actual = $this->subject->microtime();
        $this->assertEquals(microtime(true), $actual, "microtime", 100);
    }

    public function testStrtotime()
    {
        $string = 'tomorrow';

        $actual = $this->subject->strtotime($string);

        $this->assertInternalType('integer', $actual);

    }
}
