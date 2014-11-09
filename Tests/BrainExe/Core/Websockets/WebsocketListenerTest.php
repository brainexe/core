<?php

namespace Tests\BrainExe\Core\Websockets\WebsocketListener;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use BrainExe\Core\Websockets\WebsocketListener;
use Redis;
use BrainExe\Core\EventDispatcher\EventDispatcher;

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
		parent::setUp();

		$this->_mockRedis = $this->getMock(Redis::class, [], [], '', false);
		$this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

		$this->_subject = new WebsocketListener();
		$this->_subject->setRedis($this->_mockRedis);
		$this->_subject->setEventDispatcher($this->_mockEventDispatcher);
	}

	public function testGetSubscribedEvents() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->getSubscribedEvents();
	}

	public function testHandlePushEvent() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->handlePushEvent($event);
	}

}
