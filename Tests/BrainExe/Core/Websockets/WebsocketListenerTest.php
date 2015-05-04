<?php

namespace Tests\BrainExe\Core\Websockets\WebsocketListener;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Redis\RedisInterface;
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
     * @var RedisInterface|MockObject
     */
    private $mockRedis;

    /**
     * @var EventDispatcher|MockObject
     */
    private $mockEventDispatcher;


    public function setUp()
    {
        $this->mockRedis = $this->getRedisMock();
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
}
