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
        return $this->getMockBuilder(Predis::class)
            ->setMethods([
                'sadd', 'smembers', 'srem', 'get', 'setex', 'script', 'getlastError',
                'multi', 'exec', 'execute', 'exists', 'pipeline', 'incr',
                'hgetall', 'hmset', 'hmget', 'hset', 'hdel', 'hget', 'hincrby', 'set',
                'evalsha', 'load', 'publish', 'subscribe', 'info', 'hincrbyfloat', 'lrem',
                'del', 'add', 'keys', 'brpop', 'lpush', 'runmessagequeue', 'llen', 'lrange',
                'zrangebyscore', 'zcard', 'zrevrangebyscore', 'zadd', 'zincrby', 'zdeleterangebyscore', 'zrem'
            ])
            ->getMock();
    }
}
