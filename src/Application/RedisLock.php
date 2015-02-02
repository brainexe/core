<?php

namespace BrainExe\Core\Application;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\RedisTrait;

/**
 * @Service(public=false)
 */
class RedisLock
{
    const REDIS_PREFIX = 'lock:';

    use RedisTrait;

    /**
     * @param string $name
     * @param integer $lockTime
     * @return boolean $got_lock
     */
    public function lock($name, $lockTime)
    {
        $redis = $this->getRedis();

        $exists = $redis->EXISTS(self::REDIS_PREFIX . $name);
        if (!$exists) {
            $redis->SETEX($name, $lockTime, 1);
            return true;
        }

        return false;
    }

    /**
     * @param string $name
     */
    public function unlock($name)
    {
        $this->getRedis()->DEL(self::REDIS_PREFIX . $name);
    }
}
