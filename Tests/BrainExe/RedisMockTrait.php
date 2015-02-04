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
            'HMSET', 'sadd', 'smembers', 'multi',
            'hgetall', 'execute',
            'del', 'add',
            'zrangebyscore'
        ], [], '', false);
    }
}
