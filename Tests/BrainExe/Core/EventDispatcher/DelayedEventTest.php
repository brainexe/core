<?php

namespace Tests\BrainExe\Core\EventDispatcher;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\DelayedEvent;
use PHPUnit\Framework\TestCase;

class DelayedEventTest extends TestCase
{
    public function testConstructor()
    {
        /** @var AbstractEvent $wrapped */
        $wrapped   = $this->createMock(AbstractEvent::class);
        $timestamp = 3600;

        $event = new DelayedEvent($wrapped, $timestamp);

        $this->assertEquals($wrapped, $event->event);
        $this->assertEquals($wrapped, $event->getEvent());
        $this->assertEquals($timestamp, $event->getTimestamp());
        $this->assertEquals(DelayedEvent::DELAYED, $event->getEventName());
    }
}
