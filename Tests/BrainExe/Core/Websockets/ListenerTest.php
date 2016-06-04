<?php

namespace Tests\BrainExe\Core\Websockets;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\Redis\Predis;
use BrainExe\Core\Websockets\WebSocketEvent;
use BrainExe\Core\Websockets\Listener;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers BrainExe\Core\Websockets\Listener
 */
class ListenerTest extends TestCase
{

    use RedisMockTrait;

    /**
     * @var Listener
     */
    private $subject;

    /**
     * @var Predis|MockObject
     */
    private $redis;

    public function setUp()
    {
        $this->redis = $this->getRedisMock();

        $this->subject = new Listener();
        $this->subject->setRedis($this->redis);
    }

    public function testGetSubscribedEvents()
    {
        $events = $this->subject->getSubscribedEvents();
        $this->assertInternalType('array', $events);
    }

    public function testHandle()
    {
        /** @var AbstractEvent|MockObject $wrapped */
        $wrapped = $this->createMock(AbstractEvent::class);
        $event = new WebSocketEvent($wrapped);

        $this->redis
            ->expects($this->once())
            ->method('publish')
            ->with(Listener::CHANNEL, json_encode($event->getPayload()));

        $this->subject->handlePushEvent($event);
    }
}
