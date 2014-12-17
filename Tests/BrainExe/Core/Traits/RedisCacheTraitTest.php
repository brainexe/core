<?php

namespace BrainExe\Tests\Core\Traits;

use BrainExe\Core\Traits\RedisCacheTrait;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use BrainExe\Core\Redis\Redis;

class RedisCacheTraitTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var RedisCacheTrait
	 */
	private $_subject;

	/**
	 * @var Redis|MockObject
	 */
	private $_mockRedis;

	public function setUp() {
		$this->_mockRedis = $this->getMock(Redis::class);

		$this->_subject = $this->getMockForTrait(RedisCacheTrait::class);
		$this->_subject->setRedis($this->_mockRedis);
	}

	public function testInvalidate() {
		$key = 'key';

		$this->_mockRedis
			->expects($this->once())
			->method('DEL')
			->with($key);

		$this->_subject->invalidate($key);
	}

	public function testWrapWhenCached() {
		$key   = 'key';
		$ttl   = 100;
		$value = 'value';

		$callback = function() use ($value){
			return $value;
		};

		$this->_mockRedis
			->expects($this->once())
			->method('GET')
			->with($key)
			->willReturn(serialize($value));

		$actual_result = $this->_subject->wrapCache($key, $callback, $ttl);

		$this->assertEquals($value, $actual_result);
	}

	public function testWrap() {
		$key   = 'key';
		$ttl   = 100;
		$value = 'value';

		$callback = function() use ($value){
			return $value;
		};

		$this->_mockRedis
			->expects($this->once())
			->method('GET')
			->with($key)
			->willReturn(null);

		$this->_mockRedis
			->expects($this->once())
			->method('SETEX')
			->with($key, $ttl, serialize($value));

		$actual_result = $this->_subject->wrapCache($key, $callback, $ttl);

		$this->assertEquals($value, $actual_result);
	}
}
