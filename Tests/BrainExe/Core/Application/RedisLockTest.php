<?php

namespace Tests\BrainExe\Core\Application\RedisLock;

use BrainExe\Core\Application\RedisLock;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Redis;

/**
 * @Covers BrainExe\Core\Application\RedisLock
 */
class RedisLockTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var RedisLock
	 */
	private $_subject;

	/**
	 * @var Redis|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockRedis;

	public function setUp() {

		$this->_mockRedis = $this->getMock(Redis::class, [], [], '', false);
		$this->_subject = new RedisLock();
		$this->_subject->setRedis($this->_mockRedis);
	}

	public function testLock() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$name = null;
		$lock_time = null;
		$actual_result = $this->_subject->lock($name, $lock_time);
	}

	public function testUnlock() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$name = null;
		$this->_subject->unlock($name);
	}

}
