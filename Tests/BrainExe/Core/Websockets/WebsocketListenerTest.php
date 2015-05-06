<?php

namespace Tests\BrainExe\Core\Websockets\WebsocketListener;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Redis\Predis;
use BrainExe\Core\Websockets\WebSocketEvent;
use BrainExe\Core\Websockets\WebsocketListener;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers BrainExe\Core\Websockets\WebsocketListener
 */
class WebsocketListenerTest extends TestCase
{

    use RedisMockTrait;

    /**
     * @var WebsocketListener
     */
    private $subject;

    /**
     * @var Predis|MockObject
     */
    private $redis;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;


    public function setUp()
    {
        $this->redis = $this->getRedisMock();
        $this->dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new WebsocketListener();
        $this->subject->setRedis($this->redis);
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testGetSubscribedEvents()
    {
        $events = $this->subject->getSubscribedEvents();
        $this->assertInternalType('array', $events);
    }

    public function testHandle()
    {
        /** @var AbstractEvent|MockObject $wrapped */
        $wrapped = $this->getMock(AbstractEvent::class, [], [], '', false);
        $event = new WebSocketEvent($wrapped);

        $this->redis
            ->expects($this->once())
            ->method('publish')
            ->with(WebsocketListener::CHANNEL, json_encode($event->payload));

        $this->subject->handlePushEvent($event);
    }
}
