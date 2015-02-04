<?php

namespace BrainExe\Tests;

use BrainExe\Core\Redis\RedisInterface;
use PHPUnit_Framework_MockObject_MockObject;

trait RedisMockTrait {

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function getRedisMock() {
        return $this->getMock(RedisInterface::class, [
            'sadd', 'smembers',  'srem',
            'multi', 'exec', 'execute',
            'hgetall', 'hmset', 'hset', 'hdel', 'hget',
            'evalsha', 'load',
            'del', 'add', 'keys', 'brPop', 'lpush',
            'zrangebyscore', 'zcard', 'zRevRangeByScore', 'zadd', 'zDeleteRangeByScore', 'zrem'
        ], [], '', false);
    }
}
