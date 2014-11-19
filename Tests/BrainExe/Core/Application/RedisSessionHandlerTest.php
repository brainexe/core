<?php

namespace Tests\BrainExe\Core\Application\RedisSessionHandler;

use BrainExe\Core\Application\RedisSessionHandler;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Redis;

/**
 * @Covers BrainExe\Core\Application\RedisSessionHandler
 */
class RedisSessionHandlerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var RedisSessionHandler
	 */
	private $_subject;

	/**
	 * @var Redis|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockRedis;

	public function setUp() {

		$this->_mockRedis = $this->getMock(Redis::class, [], [], '', false);
		$this->_subject = new RedisSessionHandler();
		$this->_subject->setRedis($this->_mockRedis);
	}

	public function testOpen() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$savePath = null;
		$sessionName = null;
		$this->_subject->open($savePath, $sessionName);
	}

	public function testClose() {
		$this->markTestIncomplete('This is only a dummy implementation');


		$this->_subject->close();
	}

	public function testRead() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$session_id = null;
		$this->_subject->read($session_id);
	}

	public function testWrite() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$session_id = null;
		$data = null;
		$this->_subject->write($session_id, $data);
	}

	public function testDestroy() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$session_id = null;
		$this->_subject->destroy($session_id);
	}

	public function testGc() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$lifetime = null;
		$this->_subject->gc($lifetime);
	}

}
