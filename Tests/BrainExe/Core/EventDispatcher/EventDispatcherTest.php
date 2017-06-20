<?php

namespace Tests\BrainExe\Core\EventDispatcher;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\BackgroundEvent;
use BrainExe\Core\EventDispatcher\DelayedEvent;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\EventDispatcher\PushViaWebsocket;
use BrainExe\Core\Traits\JsonSerializableTrait;
use BrainExe\Core\Websockets\WebSocketEvent;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;

class TestEvent extends AbstractEvent
{
    const TYPE = 'test';
}

class TestWebsocketEvent extends AbstractEvent implements PushViaWebsocket
{
    use JsonSerializableTrait;

    const TYPE = 'websocket.test';
}

class EventDispatcherTest extends TestCase
{

    /**
     * @var EventDispatcher|MockObject
     */
    private $subject;

    public function setUp()
    {
        $this->subject = $this->getMockBuilder(EventDispatcher::class)
            ->setMethods(['dispatch'])
            ->getMock();
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

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage You have to pass an Event into EventDispatcher::dispatch
     */
    public function testDispatchEmpty()
    {
        $this->subject = $this->getMockBuilder(EventDispatcher::class)
            ->setMethods(['dispatchEvent'])
            ->getMock();

        $this->subject->dispatch(TestEvent::TYPE, null);
    }

    public function testCatchAll()
    {
        $this->subject->addCatchall($this->subject);
    }

    public function testDispatch()
    {
        $this->subject = $this->createMock(EventDispatcher::class);

        $event = new TestWebsocketEvent(TestWebsocketEvent::TYPE);

        $this->subject->dispatch(TestEvent::TYPE, $event);
    }

    public function testDispatchAsWebsocketEvent()
    {
        $event = new TestWebsocketEvent(TestWebsocketEvent::TYPE);

        $wrappedEvent = new WebSocketEvent($event);

        $this->subject
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(WebSocketEvent::PUSH, $wrappedEvent);

        $this->subject
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(TestWebsocketEvent::TYPE, $event);

        $this->subject->dispatchEvent($event);
    }

    public function testDispatchInBackground()
    {
        $event = new TestEvent(TestEvent::TYPE);
        $timestamp = 0;

        $wrappedEvent = new BackgroundEvent($event);

        $this->subject
            ->expects($this->once())
            ->method('dispatch')
            ->with(BackgroundEvent::BACKGROUND, $wrappedEvent);

        $this->subject->dispatchInBackground($event, $timestamp);
    }

    public function testDispatchInBackgroundWithTime()
    {
        $event     = new TestEvent(TestEvent::TYPE);
        $timestamp = 10;

        $wrappedEvent = new DelayedEvent($event, $timestamp);

        $this->subject
            ->expects($this->once())
            ->method('dispatch')
            ->with(DelayedEvent::DELAYED, $wrappedEvent);

        $this->subject->dispatchInBackground($event, $timestamp);
    }
}
