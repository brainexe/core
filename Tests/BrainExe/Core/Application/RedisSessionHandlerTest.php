<?php

namespace BrainExe\Tests\Core\Application;

use BrainExe\Core\Application\RedisSessionHandler;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Redis;

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

	public function testReadSession() {
		$payload    = 'foobar';
		$session_id = '121212';

		$this->_mockRedis
			->expects($this->once())
			->method('get')
			->with("session:$session_id")
			->will($this->returnValue($payload));

		$actual_result = $this->_subject->read($session_id);

		$this->assertEquals($payload, $actual_result);
	}

	public function testWriteSession() {
		$payload    = 'foobar';
		$session_id = '121212';

		$this->_subject->open(null, $session_id);

		$this->_mockRedis
			->expects($this->once())
			->method('setex')
			->with("session:$session_id", $this->isType('integer'), $payload);

		$this->_subject->write($session_id, $payload);
	}

	public function testDestroySession() {
		$session_id = '121212';

		$this->_mockRedis
			->expects($this->once())
			->method('del')
			->with("session:$session_id");

		$this->_subject->destroy($session_id);
		$this->_subject->close();
		$this->_subject->gc(0);

	}
} 