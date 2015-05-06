<?php

namespace BrainExe\Tests;

use BrainExe\Core\Redis\Predis;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

trait RedisMockTrait
{

    /**
     * @return MockObject
     */
    protected function getRedisMock()
    {
        return $this->getMock(Predis::class, [
            'sadd', 'smembers', 'srem', 'get', 'setex', 'script', 'getLastError',
            'multi', 'exec', 'execute', 'exists', 'pipeline',
            'hgetall', 'hmset', 'hset', 'hdel', 'hget', 'hincrby',
            'evalsha', 'load', 'publish', 'subscribe',
            'del', 'add', 'keys', 'brPop', 'lpush',
            'zrangebyscore', 'zcard', 'zRevRangeByScore', 'zadd', 'zDeleteRangeByScore', 'zrem'
        ], [], '', false);
    }
}
