<?php

namespace BrainExe\Tests;

use BrainExe\Core\Redis\RedisInterface;
use PHPUnit_Framework_MockObject_MockObject;

trait RedisMockTrait
{

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function getRedisMock()
    {
        return $this->getMock(RedisInterface::class, [
            'sadd', 'smembers',  'srem', 'get', 'setex', 'script', 'getLastError',
            'multi', 'exec', 'execute', 'exists',
            'hgetall', 'hmset', 'hset', 'hdel', 'hget', 'hincrby',
            'evalsha', 'load', 'publish', 'subscribe',
            'del', 'add', 'keys', 'brPop', 'lpush',
            'zrangebyscore', 'zcard', 'zRevRangeByScore', 'zadd', 'zDeleteRangeByScore', 'zrem'
        ], [], '', false);
    }
}
