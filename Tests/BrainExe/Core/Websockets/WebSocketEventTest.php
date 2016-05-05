<?php

namespace Tests\BrainExe\Core\Websockets;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\Websockets\WebSocketEvent;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers BrainExe\Core\Websockets\WebSocketEvent
 */
class WebSocketEventTest extends TestCase
{

    public function testGetSubscribedEvents()
    {
        /** @var AbstractEvent $event */
        $event = $this->getMockWithoutInvokingTheOriginalConstructor(AbstractEvent::class);

        $subject = new WebSocketEvent($event);

        $this->assertEquals($event, $subject->getPayload());
    }
}
