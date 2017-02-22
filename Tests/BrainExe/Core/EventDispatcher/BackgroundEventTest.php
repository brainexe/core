<?php

namespace Tests\BrainExe\Core\EventDispatcher;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\BackgroundEvent;
use PHPUnit\Framework\TestCase;

class BackgroundEventTest extends TestCase
{
    public function testConstructor()
    {
        /** @var AbstractEvent $wrapped */
        $wrapped = $this->createMock(AbstractEvent::class);

        $event = new BackgroundEvent($wrapped);

        $this->assertEquals($wrapped, $event->event);
        $this->assertEquals($wrapped, $event->getEvent());
        $this->assertEquals(BackgroundEvent::BACKGROUND, $event->getEventName());
    }
}
