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
class RedisCacheTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var RedisCache
     */
    private $subject;

    /**
     * @var MockObject|Redis
     */
    private $mockRedis;

    public function setUp()
    {
        $this->mockRedis = $this->getMock(Redis::class, [], [], '', false);

        $this->subject = new RedisCache($this->mockRedis);
    }

    public function testSaveWithOutTTL()
    {
        $cacheId = 'id';
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

        $actualResult = $this->subject->save($cacheId, $data, $ttl);

        $this->assertFalse($actualResult);
    }
    public function testSaveWithTTL()
    {
        $cacheId = 'id';
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

        $actualResult = $this->subject->save($cacheId, $data, $ttl);

        $this->assertFalse($actualResult);
    }

    public function testGetNotExistingShouldReturnFalse()
    {
        $cacheId = 'id';

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

        $actualResult = $this->subject->fetch($cacheId);

        $this->assertFalse($actualResult);
    }

    public function testGet()
    {
        $cacheId = 'id';
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

        $actualResult = $this->subject->fetch($cacheId);

        $this->assertEquals($data, $actualResult);
    }

    public function testContains()
    {
        $cacheId = 'id';

        $this->mockRedis
        ->expects($this->once())
        ->method('exists')
        ->with('[id][1]')
        ->willReturn(true);

        $actualResult = $this->subject->contains($cacheId);

        $this->assertTrue($actualResult);
    }

    public function testDelete()
    {
        $cacheId = 'id';

        $this->mockRedis
        ->expects($this->once())
        ->method('del')
        ->with('[id][1]');

        $this->subject->delete($cacheId);
    }

    public function testFlushAll()
    {
        $this->mockRedis
        ->expects($this->once())
        ->method('flushdb');

        $this->subject->flushAll();
    }

    public function testGetStats()
    {
        $hits       = 20;
        $misses     = 10;
        $uptime     = 3600;
        $usedMemory = 10000;

        $info = [
            'keyspace_hits' => $hits,
            'keyspace_misses' => $misses,
            'uptime_in_seconds' => $uptime,
            'used_memory' => $usedMemory,
        ];

        $this->mockRedis
            ->expects($this->once())
            ->method('info')
            ->willReturn($info);

        $actualResult = $this->subject->getStats();

        $expectedResult = [
            Cache::STATS_HITS => $hits,
            Cache::STATS_MISSES => $misses,
            Cache::STATS_UPTIME => $uptime,
            Cache::STATS_MEMORY_USAGE => $usedMemory,
            Cache::STATS_MEMORY_AVAILIABLE => null,
        ];
        ;

        $this->assertEquals($expectedResult, $actualResult);
    }
}
