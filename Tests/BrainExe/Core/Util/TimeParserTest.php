<?php

namespace BrainExe\Tests\Core\Util;

use BrainExe\Core\Application\UserException;
use BrainExe\Core\Util\TimeParser;
use PHPUnit_Framework_TestCase;

class TimeParserTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var TimeParser
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new TimeParser();
    }

    /**
     * @dataProvider providerTimes
     * @param string $inputString
     * @param integer $expectedEta
     */
    public function testParse($inputString, $expectedEta)
    {
        if (false === $expectedEta) {
            $this->setExpectedException(UserException::class);
        }

        $now = time();
        $actualSeconds = $this->subject->parseString($inputString);

        $this->assertEquals($now + $expectedEta, $actualSeconds, "time parser", 2);
    }

    public function testParseWithEmptyTime()
    {
        $actualSeconds = $this->subject->parseString(0);
        $this->assertEquals(0, $actualSeconds);
    }

    /**
     * @return array[]
     */
    public static function providerTimes()
    {
        return [
            [2, 2],
            [-1, false],
            ["2", 2],
            ['5s', 5],
            ['10S', 10],
            ['5t', false],
            ['7m', 7*60],
            ['now', 0]
        ];
    }
}
