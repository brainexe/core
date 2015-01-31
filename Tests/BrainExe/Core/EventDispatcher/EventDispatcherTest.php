<?php

namespace Tests\BrainExe\Core\EventDispatcher\EventDispatcher;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\BackgroundEvent;
use BrainExe\Core\EventDispatcher\DelayedEvent;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\EventDispatcher\PushViaWebsocketInterface;
use BrainExe\Core\Websockets\WebSocketEvent;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\Container;

class TestEvent extends AbstractEvent
{
    const TYPE = 'test';
}

class TestWebsocketEvent extends AbstractEvent implements PushViaWebsocketInterface
{
    const TYPE = 'websocket.test';
}

class EventDispatcherTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var EventDispatcher|MockObject
     */
    private $subject;

    /**
     * @var Container|MockObject
     */
    private $mockContainer;

    public function setUp()
    {
        $this->mockContainer = $this->getMock(Container::class, [], [], '', false);
        $this->subject = $this->getMock(EventDispatcher::class, ['dispatch'], [], '', false);
    }

    public function testDispatchEvent()
    {
        $event = new TestEvent(TestEvent::TYPE);

        $this->subject
            ->expects($this->once())
            ->method('dispatch')
            ->with(TestEvent::TYPE, $event);

        $this->subject->dispatchEvent($event);
    }

    public function testDispatchAsWebsocketEvent()
    {
        $event = new TestWebsocketEvent(TestWebsocketEvent::TYPE);

        $wrappedEvent = new WebSocketEvent($event);

        $this->subject
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(TestWebsocketEvent::TYPE, $event);

        $this->subject
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(WebSocketEvent::PUSH, $wrappedEvent);

        $this->subject->dispatchEvent($event);
    }

    public function testDispatchInBackground()
    {
        $this->subject->setEnabled(true);

        $event = new TestEvent(TestEvent::TYPE);
        $timestamp = 0;

        $wrappedEvent = new BackgroundEvent($event);

        $this->subject
            ->expects($this->once())
            ->method('dispatch')
            ->with(BackgroundEvent::BACKGROUND, $wrappedEvent);

        $this->subject->dispatchInBackground($event, $timestamp);
    }

    public function testDispatchWhenNotEnabled()
    {
        $this->subject->setEnabled(false);

        $event = new TestEvent(TestEvent::TYPE);
        $timestamp = 0;

        $this->subject
            ->expects($this->once())
            ->method('dispatch')
            ->with(BackgroundEvent::BACKGROUND, $event);

        $this->subject->dispatchInBackground($event, $timestamp);
    }

    public function testDispatchInBackgroundWithTime()
    {
        $event = new TestEvent(TestEvent::TYPE);
        $timestamp = 10;

        $wrappedEvent = new DelayedEvent($event, $timestamp);

        $this->subject
            ->expects($this->once())
            ->method('dispatch')
            ->with(DelayedEvent::DELAYED, $wrappedEvent);

        $this->subject->dispatchInBackground($event, $timestamp);
    }
}
