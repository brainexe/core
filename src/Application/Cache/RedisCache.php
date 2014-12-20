<?php

namespace BrainExe\Core\Application\Cache;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\CacheProvider;
use BrainExe\Core\Redis\Redis;

class RedisCache extends CacheProvider
{
    /**
     * @var Redis
     */
    protected $redis;

    /**
     * Sets the redis instance to use.
     *
     * @param Redis $redis
     */
    public function __construct($redis)
    {
        $this->redis = $redis;
    }

    /**
     * {@inheritdoc}
     */
    protected function doFetch($id)
    {
        $result = $this->redis->get($id);

        return null === $result ? false : unserialize($result);
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        return (bool)$this->redis->exists($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $life_time = false)
    {
        if (0 < $life_time) {
            $result = $this->redis->setex($id, (int)$life_time, serialize($data));
        } else {
            $result = $this->redis->set($id, serialize($data));
        }

        return (bool)$result;
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        return (bool)$this->redis->del($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doFlush()
    {
        return (bool)$this->redis->flushdb();
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetStats()
    {
        $stats = $this->redis->info();

        return [
        Cache::STATS_HITS => isset($stats['keyspace_hits']) ? $stats['keyspace_hits'] : $stats['Stats']['keyspace_hits'],
        Cache::STATS_MISSES => isset($stats['keyspace_misses']) ? $stats['keyspace_misses'] : $stats['Stats']['keyspace_misses'],
        Cache::STATS_UPTIME => isset($stats['uptime_in_seconds']) ? $stats['uptime_in_seconds'] : $stats['Server']['uptime_in_seconds'],
        Cache::STATS_MEMORY_USAGE => isset($stats['used_memory']) ? $stats['used_memory'] : $stats['Memory']['used_memory'],
        Cache::STATS_MEMORY_AVAILIABLE => null,
        ];
    }
}
