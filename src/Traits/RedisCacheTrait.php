<?php

namespace BrainExe\Core\Traits;

/**
 * @api
 * @deprecated
 */
trait RedisCacheTrait
{

    use RedisTrait;

    /**
     * @param string $key
     * @param callable $callback
     * @param int $ttl
     * @return mixed
     */
    public function wrapCache($key, callable $callback, $ttl = 3600)
    {
        $cachedValue = $this->redis->get($key);
        if ($cachedValue) {
            return unserialize($cachedValue);
        }

        $value = $callback();

        $this->redis->setex($key, $ttl, serialize($value));

        return $value;
    }

    /**
     * @param string $key
     */
    public function invalidate($key)
    {
        $this->redis->del($key);
    }
}
