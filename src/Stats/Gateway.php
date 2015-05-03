<?php

namespace BrainExe\Core\Stats;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\RedisTrait;

/**
 * @Service("Stats.Gateway", public=false)
 */
class Gateway
{

    const KEY = 'stats';

    use RedisTrait;

    /**
     * @param string $key
     * @param int $value
     */
    public function increase($key, $value = 1)
    {
        $this->getRedis()->hincrby(self::KEY, $key, $value);
    }

    /**
     * @param string $key
     * @param int $value
     */
    public function set($key, $value)
    {
        $this->getRedis()->hset(self::KEY, $key, $value);
    }

    /**
     * @return int[]
     */
    public function getAll()
    {
        return $this->getRedis()->hgetall(self::KEY);
    }

    /**
     * @param string $key
     * @return int
     */
    public function get($key)
    {
        return $this->getRedis()->hget(self::KEY, $key);
    }
}
