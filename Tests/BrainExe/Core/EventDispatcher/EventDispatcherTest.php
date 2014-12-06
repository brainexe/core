<?php

namespace Tests\BrainExe\Core\EventDispatcher\EventDispatcher;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\BackgroundEvent;
use BrainExe\Core\EventDispatcher\DelayedEvent;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\EventDispatcher\PushViaWebsocketInterface;
use BrainExe\Core\Websockets\WebSocketEvent;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\Container;

class TestEvent extends AbstractEvent {
	const TYPE = 'test';
}

class TestWebsocketEvent extends AbstractEvent implements PushViaWebsocketInterface {
	const TYPE = 'websocket.test';
}

class EventDispatcherTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var EventDispatcher|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_subject;

	/**
	 * @var Container|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockContainer;

	public function setUp() {
		$this->_mockContainer = $this->getMock(Container::class, [], [], '', false);

		$this->_subject = $this->getMock(EventDispatcher::class, ['dispatch'], [], '', false);
	}

	public function testDispatchEvent() {
		$event = new TestEvent(TestEvent::TYPE);

		$this->_subject
			->expects($this->once())
			->method('dispatch')
			->with(TestEvent::TYPE, $event);

		$this->_subject->dispatchEvent($event);
	}

	public function testDispatchAsWebsocketEvent() {
		$event = new TestWebsocketEvent(TestWebsocketEvent::TYPE);

		$wrapped_event = new WebSocketEvent($event);

		$this->_subject
			->expects($this->at(0))
			->method('dispatch')
			->with(TestWebsocketEvent::TYPE, $event);

		$this->_subject
			->expects($this->at(1))
			->method('dispatch')
			->with(WebSocketEvent::PUSH, $wrapped_event);

		$this->_subject->dispatchEvent($event);
	}

	public function testDispatchInBackground() {
		$event = new TestEvent(TestEvent::TYPE);
		$timestamp = 0;

		$wrapped_event = new BackgroundEvent($event);

		$this->_subject
			->expects($this->once())
			->method('dispatch')
			->with(BackgroundEvent::BACKGROUND, $wrapped_event);

		$this->_subject->dispatchInBackground($event, $timestamp);
	}

	public function testDispatchInBackgroundWithTime() {
		$event = new TestEvent(TestEvent::TYPE);
		$timestamp = 10;

		$wrapped_event = new DelayedEvent($event, $timestamp);

		$this->_subject
			->expects($this->once())
			->method('dispatch')
			->with(DelayedEvent::DELAYED, $wrapped_event);

		$this->_subject->dispatchInBackground($event, $timestamp);
	}

}
