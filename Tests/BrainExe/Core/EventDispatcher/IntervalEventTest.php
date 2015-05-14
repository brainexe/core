<?php

namespace Tests\BrainExe\Core\EventDispatcher;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\IntervalEvent;
use PHPUnit_Framework_TestCase as TestCase;

class IntervalEventTest extends TestCase
{
    public function testConstructor()
    {
        /** @var AbstractEvent $wrapped */

        $wrapped   = $this->getMock(AbstractEvent::class, [], [], '', false);
        $timestamp = 10000;
        $interval  = 3600;

        $event = new IntervalEvent($wrapped, $timestamp, $interval);

        $this->assertEquals($wrapped, $event->event);
        $this->assertEquals($timestamp, $event->timestamp);
        $this->assertEquals($interval, $event->interval);
    }
}
