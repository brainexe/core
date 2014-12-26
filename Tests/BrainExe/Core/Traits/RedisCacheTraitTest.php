<?php

namespace BrainExe\Tests\Core\Traits;

use BrainExe\Core\Traits\RedisCacheTrait;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use BrainExe\Core\Redis\Redis;

class RedisCacheTraitTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var RedisCacheTrait
     */
    private $subject;

    /**
     * @var Redis|MockObject
     */
    private $mockRedis;

    public function setUp()
    {
        $this->mockRedis = $this->getMock(Redis::class);

        $this->subject = $this->getMockForTrait(RedisCacheTrait::class);
        $this->subject->setRedis($this->mockRedis);
    }

    public function testInvalidate()
    {
        $key = 'key';

        $this->mockRedis
        ->expects($this->once())
        ->method('DEL')
        ->with($key);

        $this->subject->invalidate($key);
    }

    public function testWrapWhenCached()
    {
        $key   = 'key';
        $ttl   = 100;
        $value = 'value';

        $callback = function() use ($value) {
            return $value;
        };

        $this->mockRedis
        ->expects($this->once())
        ->method('GET')
        ->with($key)
        ->willReturn(serialize($value));

        $actualResult = $this->subject->wrapCache($key, $callback, $ttl);

        $this->assertEquals($value, $actualResult);
    }

    public function testWrap()
    {
        $key   = 'key';
        $ttl   = 100;
        $value = 'value';

        $callback = function() use ($value) {
            return $value;
        };

        $this->mockRedis
        ->expects($this->once())
        ->method('GET')
        ->with($key)
        ->willReturn(null);

        $this->mockRedis
        ->expects($this->once())
        ->method('SETEX')
        ->with($key, $ttl, serialize($value));

        $actualResult = $this->subject->wrapCache($key, $callback, $ttl);

        $this->assertEquals($value, $actualResult);
    }
}
