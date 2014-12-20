<?php

namespace Tests\BrainExe\Core\Websockets\WebsocketListener;

use BrainExe\Core\Application\SelfUpdate\SelfUpdateEvent;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Websockets\WebSocketEvent;
use BrainExe\Core\Websockets\WebsocketListener;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use BrainExe\Core\Redis\Redis;

/**
 * @Covers BrainExe\Core\Websockets\WebsocketListener
 */
class WebsocketListenerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var WebsocketListener
     */
    private $subject;

    /**
     * @var Redis|MockObject
     */
    private $mockRedis;

    /**
     * @var EventDispatcher|MockObject
     */
    private $mockEventDispatcher;


    public function setUp()
    {
        $this->mockRedis = $this->getMock(Redis::class, [], [], '', false);
        $this->mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new WebsocketListener();
        $this->subject->setRedis($this->mockRedis);
        $this->subject->setEventDispatcher($this->mockEventDispatcher);
    }

    public function testGetSubscribedEvents()
    {
        $events = $this->subject->getSubscribedEvents();
        $this->assertInternalType('array', $events);
    }

    public function testHandlePushEvent()
    {
        $payload = new SelfUpdateEvent(SelfUpdateEvent::TRIGGER);
        $event = new WebSocketEvent($payload);

        $this->mockRedis
        ->expects($this->once())
        ->method('publish')
        ->with(WebsocketListener::CHANNEL, json_encode($payload));

        $this->subject->handlePushEvent($event);
    }
}
