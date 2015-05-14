<?php

namespace BrainExe\Tests\Core\Traits;

use BrainExe\Core\Redis\Predis;
use BrainExe\Core\Traits\RedisCacheTrait;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;

class RedisCacheTraitTest extends PHPUnit_Framework_TestCase
{

    use RedisMockTrait;

    /**
     * @var RedisCacheTrait
     */
    private $subject;

    /**
     * @var Predis|MockObject
     */
    private $redis;

    public function setUp()
    {
        $this->redis = $this->getRedisMock();

        $this->subject = $this->getMockForTrait(RedisCacheTrait::class);
        $this->subject->setRedis($this->redis);
    }

    public function testInvalidate()
    {
        $key = 'key';

        $this->redis
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

        $this->redis
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

        $this->redis
            ->expects($this->once())
            ->method('GET')
            ->with($key)
            ->willReturn(null);

        $this->redis
            ->expects($this->once())
            ->method('SETEX')
            ->with($key, $ttl, serialize($value));

        $actualResult = $this->subject->wrapCache($key, $callback, $ttl);

        $this->assertEquals($value, $actualResult);
    }
}
