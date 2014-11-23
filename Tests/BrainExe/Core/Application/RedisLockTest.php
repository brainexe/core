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

	public function testLockWhenNotLockedYet() {
		$name = 'lock';
		$lock_time = 10;

		$this->_mockRedis
			->expects($this->once())
			->method('EXISTS')
			->with("lock:$name")
			->will($this->returnValue(false));

		$this->_mockRedis
			->expects($this->once())
			->method('SETEX')
			->with($name, $lock_time)
			->will($this->returnValue(true));

		$actual_result = $this->_subject->lock($name, $lock_time);

		$this->assertTrue($actual_result);
	}

	public function testLockWhenLocked() {
		$name = 'lock';
		$lock_time = 10;

		$this->_mockRedis
			->expects($this->once())
			->method('EXISTS')
			->with("lock:$name")
			->will($this->returnValue(true));

		$actual_result = $this->_subject->lock($name, $lock_time);

		$this->assertFalse($actual_result);
	}

	public function testUnlock() {
		$name = 'name';

		$this->_mockRedis
			->expects($this->once())
			->method('del')
			->with("lock:$name");

		$this->_subject->unlock($name);
	}

}
