<?php

namespace BrainExe\Core\Stats;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\RedisTrait;

/**
 * @Service("Stats.Gateway", public=false)
 */
class Gateway
{
    const KEY = 'statistics';

    use RedisTrait;

    /**
     * @param int[] $values
     */
    public function increase(array $values)
    {
        $pipeline = $this->getRedis()->pipeline(['fire-and-forget' => true]);
        foreach ($values as $key => $value) {
            $pipeline->zincrby(self::KEY, $value, $key);
        }
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
            $pipeline->zrem(self::KEY, $key);
        }

        $pipeline->execute();
    }

    /**
     * @return int[]
     */
    public function getAll()
    {
        return $this->getRedis()->zrevrangebyscore(self::KEY, 0, '+inf', ['withscores' => true]);
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
