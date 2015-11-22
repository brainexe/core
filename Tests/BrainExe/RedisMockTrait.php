<?php

namespace BrainExe\Tests;

use BrainExe\Core\Redis\Predis;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

trait RedisMockTrait
{

    /**
     * @return MockObject|Predis
     */
    protected function getRedisMock()
    {
        return $this->getMock(Predis::class, [
            'sadd', 'smembers', 'srem', 'get', 'setex', 'script', 'getLastError',
            'multi', 'exec', 'execute', 'exists', 'pipeline', 'hmget', 'incr',
            'hgetall', 'hmset', 'hset', 'hdel', 'hget', 'hincrby', 'set',
            'evalsha', 'load', 'publish', 'subscribe', 'info', 'hincrbyfloat', 'lrem',
            'del', 'add', 'keys', 'brPop', 'lpush', 'runmessagequeue', 'LLEN', 'lrange',
            'zrangebyscore', 'zcard', 'zRevRangeByScore', 'zadd', 'zincrby', 'zDeleteRangeByScore', 'zrem'
        ], [], '', false);
    }
}
