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
        $pipeline = $this->getRedis()->pipeline(['fire-and-forget' => true]);
        $pipeline->hincrby(self::KEY, $key, $value);
        $pipeline->execute();
    }

    /**
     * @param string $key
     * @param int $value
     */
    public function set($key, $value)
    {
        $pipeline = $this->getRedis()->pipeline(['fire-and-forget' => true]);
        if ($value) {
            $pipeline->hset(self::KEY, $key, $value);
        } else {
            $pipeline->hdel(self::KEY, $key);
        }
        $pipeline->execute();
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
