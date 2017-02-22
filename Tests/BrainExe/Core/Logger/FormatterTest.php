<?php

namespace BrainExe\Tests\Core\Logger;

use BrainExe\Core\Logger\Formatter;
use PHPUnit\Framework\TestCase;

/**
 * @covers \BrainExe\Core\Logger\Formatter
 */
class FormatterTest extends TestCase
{

    /**
     * @var Formatter
     */
    private $subject;

    public function setup()
    {
        $this->subject = new Formatter(
            null,
            null,
            false,
            true
        );
    }

    public function testFormat()
    {
        $actual = $this->subject->format([
            'message'    => 'myMessage',
            'level_name' => 'myLevelName',
            'datetime'   => '2015/04/02',
            'channel'    => 'myChannel',
            'extra'      => [],
            'context'    => ['channel' => 'myChannel']
        ]);

        $expected = '[2015/04/02] myChannel.myLevelName: myMessage  ' . PHP_EOL;

        $this->assertEquals($expected, $actual);
    }
}
