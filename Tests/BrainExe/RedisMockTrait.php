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
            'multi', 'exec', 'execute', 'exists', 'pipeline', 'hmget',
            'hgetall', 'hmset', 'hset', 'hdel', 'hget', 'hincrby', 'set',
            'evalsha', 'load', 'publish', 'subscribe', 'info', 'hincrbyfloat',
            'del', 'add', 'keys', 'brPop', 'lpush', 'runmessagequeue',
            'zrangebyscore', 'zcard', 'zRevRangeByScore', 'zadd', 'zincrby', 'zDeleteRangeByScore', 'zrem'
        ], [], '', false);
    }
}
