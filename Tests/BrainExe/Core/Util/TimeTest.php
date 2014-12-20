<?php

namespace Tests\BrainExe\Core\Util\Time;

use BrainExe\Core\Util\Time;
use PHPUnit_Framework_TestCase;

/**
 * @Covers BrainExe\Core\Util\Time
 */
class TimeTest extends PHPUnit_Framework_TestCase
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
        $actualResult = $this->subject->now();

        $this->assertEquals(time(), $actualResult, "current time", 1);
    }

    public function testDate()
    {
        $actualResult = $this->subject->date('y');
        $expectedResult = date('y');

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testMicrotime()
    {
        $actualResult = $this->subject->microtime();
        $this->assertEquals(microtime(true), $actualResult, "microtime", 100);
    }

    public function testStrtotime()
    {
        $string = 'tomorrow';

        $actualResult = $this->subject->strtotime($string);

        $this->assertInternalType('integer', $actualResult);

    }
}
