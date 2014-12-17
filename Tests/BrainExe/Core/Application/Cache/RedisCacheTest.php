<?php

namespace Tests\BrainExe\Core\Application\Cache\RedisCache;

use BrainExe\Core\Application\Cache\RedisCache;
use BrainExe\Core\Redis\Redis;
use Doctrine\Common\Cache\Cache;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;

/**
 * @Covers BrainExe\Core\Application\Cache\RedisCache
 */
class RedisCacheTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var RedisCache
	 */
	private $subject;

	/**
	 * @var MockObject|Redis
	 */
	private $mockRedis;

	public function setUp() {
		$this->mockRedis = $this->getMock(Redis::class, [], [], '', false);

		$this->subject = new RedisCache($this->mockRedis);
	}

	public function testSaveWithOutTTL() {
		$id = 'id';
		$data = 'data';
		$ttl = 0;

		$this->mockRedis
			->expects($this->at(0))
			->method('get')
			->with('DoctrineNamespaceCacheKey[]')
			->willReturn(null);

		$this->mockRedis
			->expects($this->at(1))
			->method('set');

		$this->mockRedis
			->expects($this->at(2))
			->method('set')
			->with('[id][1]', serialize($data));

		$actual_result = $this->subject->save($id, $data, $ttl);

		$this->assertFalse($actual_result);
	}
	public function testSaveWithTTL() {
		$id = 'id';
		$data = 'data';
		$ttl = 10;

		$this->mockRedis
			->expects($this->at(0))
			->method('get')
			->with('DoctrineNamespaceCacheKey[]')
			->willReturn(null);

		$this->mockRedis
			->expects($this->at(1))
			->method('set');

		$this->mockRedis
			->expects($this->at(2))
			->method('setex')
			->with('[id][1]', $ttl, serialize($data));

		$actual_result = $this->subject->save($id, $data, $ttl);

		$this->assertFalse($actual_result);
	}

	public function testGetNotExistingShouldReturnFalse() {
		$id = 'id';

		$this->mockRedis
			->expects($this->at(0))
			->method('get')
			->with('DoctrineNamespaceCacheKey[]')
			->willReturn(null);

		$this->mockRedis
			->expects($this->at(1))
			->method('save');

		$this->mockRedis
			->expects($this->at(2))
			->method('get')
			->with('[id][1]')
			->willReturn(null);

		$actual_result = $this->subject->fetch($id);

		$this->assertFalse($actual_result);
	}

	public function testGet() {
		$id = 'id';
		$data = 120;

		$this->mockRedis
			->expects($this->at(0))
			->method('get')
			->with('DoctrineNamespaceCacheKey[]')
			->willReturn(null);

		$this->mockRedis
			->expects($this->at(1))
			->method('save');

		$this->mockRedis
			->expects($this->at(2))
			->method('get')
			->with('[id][1]')
			->willReturn(serialize($data));

		$actual_result = $this->subject->fetch($id);

		$this->assertEquals($data, $actual_result);
	}

	public function testContains() {
		$id = 'id';

		$this->mockRedis
			->expects($this->once())
			->method('exists')
			->with('[id][1]')
			->willReturn(true);

		$actual_result = $this->subject->contains($id);

		$this->assertTrue($actual_result);
	}

	public function testDelete() {
		$id = 'id';

		$this->mockRedis
			->expects($this->once())
			->method('del')
			->with('[id][1]');

		$this->subject->delete($id);
	}

	public function testFlushAll() {
		$this->mockRedis
			->expects($this->once())
			->method('flushdb');

		$this->subject->flushAll();
	}

	public function testGetStats() {
		$hits             = 20;
		$misses           = 10;
		$uptime_in_second = 3600;
		$used_memory      = 10000;

		$raw_info = [
			'keyspace_hits' => $hits,
			'keyspace_misses' => $misses,
			'uptime_in_seconds' => $uptime_in_second,
			'used_memory' => $used_memory,
		];

		$this->mockRedis
			->expects($this->once())
			->method('info')
			->will($this->returnValue($raw_info));

		$actual_result = $this->subject->getStats();

		$expected_result = [
			Cache::STATS_HITS => $hits,
			Cache::STATS_MISSES => $misses,
			Cache::STATS_UPTIME => $uptime_in_second,
			Cache::STATS_MEMORY_USAGE => $used_memory,
			Cache::STATS_MEMORY_AVAILIABLE => null,
		];;

		$this->assertEquals($expected_result, $actual_result);
	}
}
