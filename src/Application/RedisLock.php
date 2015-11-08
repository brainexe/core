<?php

namespace BrainExe\Core\Application;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\RedisTrait;

/**
 * @Service(public=false)
 * @api
 */
class RedisLock
{
    const REDIS_PREFIX = 'lock:';

    use RedisTrait;

    /**
     * @param string $name
     * @param integer $lockTime
     * @return bool $got_lock
     */
    public function lock($name, $lockTime)
    {
        $redis = $this->getRedis();

        return $redis->SET(self::REDIS_PREFIX . $name, '1', 'EX', $lockTime, 'NX');
    }

    /**
     * @param string $name
     */
    public function unlock($name)
    {
        $this->getRedis()->DEL(self::REDIS_PREFIX . $name);
    }
}
