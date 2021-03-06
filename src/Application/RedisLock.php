<?php

namespace BrainExe\Core\Application;

use BrainExe\Core\Annotations\Service;
use BrainExe\Core\Traits\RedisTrait;

/**
 * @Service
 * @api
 */
class RedisLock
{
    private const PREFIX = 'lock:';

    use RedisTrait;

    /**
     * @param string $name
     * @param int $lockTime
     * @return bool $got_lock
     */
    public function lock(string $name, int $lockTime) : bool
    {
        $redis = $this->getRedis();

        return (bool)$redis->set(self::PREFIX . $name, '1', 'EX', $lockTime, 'NX');
    }

    /**
     * @param string $name
     */
    public function unlock(string $name)
    {
        $this->getRedis()->del(self::PREFIX . $name);
    }
}
