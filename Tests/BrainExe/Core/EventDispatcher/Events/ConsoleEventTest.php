<?php

namespace Tests\BrainExe\Core\EventDispatcher\Events;

use BrainExe\Core\EventDispatcher\Events\ConsoleEvent;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

class ConsoleEventTest extends TestCase
{

    public function testWithoutOutput()
    {
        $event = new ConsoleEvent('command');
        $this->assertEquals('command', $event->getCommand());
        $this->assertNull($event->getOutput());
    }

    public function testWithOutput()
    {
        $output = new BufferedOutput();

        $event = new ConsoleEvent('command', $output);
        $this->assertEquals('command', $event->getCommand());
        $this->assertEquals($output, $event->getOutput());
    }
}
