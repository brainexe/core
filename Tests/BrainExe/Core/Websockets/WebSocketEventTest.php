<?php

namespace Tests\BrainExe\Core\Websockets;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\Websockets\WebSocketEvent;
use PHPUnit\Framework\TestCase;

/**
 * @covers \BrainExe\Core\Websockets\WebSocketEvent
 */
class WebSocketEventTest extends TestCase
{

    public function testGetSubscribedEvents()
    {
        /** @var AbstractEvent $event */
        $event = $this->createMock(AbstractEvent::class);

        $subject = new WebSocketEvent($event);

        $this->assertEquals($event, $subject->getPayload());
    }
}
