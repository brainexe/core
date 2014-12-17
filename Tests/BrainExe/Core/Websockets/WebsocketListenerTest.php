<?php

namespace Tests\BrainExe\Core\Websockets\WebsocketListener;

use BrainExe\Core\Application\SelfUpdate\SelfUpdateEvent;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Websockets\WebSocketEvent;
use BrainExe\Core\Websockets\WebsocketListener;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use BrainExe\Core\Redis\Redis;

/**
 * @Covers BrainExe\Core\Websockets\WebsocketListener
 */
class WebsocketListenerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var WebsocketListener
	 */
	private $_subject;

	/**
	 * @var Redis|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockRedis;

	/**
	 * @var EventDispatcher|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockEventDispatcher;


	public function setUp() {
		$this->_mockRedis = $this->getMock(Redis::class, [], [], '', false);
		$this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

		$this->_subject = new WebsocketListener();
		$this->_subject->setRedis($this->_mockRedis);
		$this->_subject->setEventDispatcher($this->_mockEventDispatcher);
	}

	public function testGetSubscribedEvents() {
		$events = $this->_subject->getSubscribedEvents();
		$this->assertInternalType('array', $events);
	}

	public function testHandlePushEvent() {
		$payload = new SelfUpdateEvent(SelfUpdateEvent::TRIGGER);
		$event = new WebSocketEvent($payload);

		$this->_mockRedis
			->expects($this->once())
			->method('publish')
			->with(WebsocketListener::CHANNEL, json_encode($payload));

		$this->_subject->handlePushEvent($event);
	}

}
