<?php

namespace Tests\BrainExe\Core\EventDispatcher;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\BackgroundEvent;
use PHPUnit_Framework_TestCase as TestCase;

class BackgroundEventTest extends TestCase
{
    public function testConstructor()
    {
        /** @var AbstractEvent $wrapped */
        $wrapped = $this->getMockWithoutInvokingTheOriginalConstructor(AbstractEvent::class);

        $event = new BackgroundEvent($wrapped);

        $this->assertEquals($wrapped, $event->event);
        $this->assertEquals($wrapped, $event->getEvent());
        $this->assertEquals(BackgroundEvent::BACKGROUND, $event->getEventName());
    }
}
