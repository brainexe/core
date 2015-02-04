<?php

namespace BrainExe\Core\Application\Cache;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\CacheProvider;
use BrainExe\Core\Redis\PhpRedis;

/**
 * @todo use native cache provider from doctrine :)
 */
class RedisCache extends CacheProvider
{
    /**
     * @var PhpRedis
     */
    private $redis;

    /**
     * Sets the redis instance to use.
     *
     * @param PhpRedis $redis
     */
    public function __construct($redis)
    {
        $this->redis = $redis;
    }

    /**
     * {@inheritdoc}
     */
    protected function doFetch($cacheId)
    {
        $result = $this->redis->get($cacheId);

        return null === $result ? false : unserialize($result);
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($cacheId)
    {
        return (bool)$this->redis->exists($cacheId);
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($cacheId, $data, $lifeTime = false)
    {
        if (0 < $lifeTime) {
            $result = $this->redis->setex($cacheId, (int)$lifeTime, serialize($data));
        } else {
            $result = $this->redis->set($cacheId, serialize($data));
        }

        return (bool)$result;
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($cacheId)
    {
        return (bool)$this->redis->del($cacheId);
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
            Cache::STATS_HITS =>isset($stats['keyspace_hits']) ?
                $stats['keyspace_hits'] :
                $stats['Stats']['keyspace_hits'],
            Cache::STATS_MISSES => isset($stats['keyspace_misses']) ?
                $stats['keyspace_misses'] :
                $stats['Stats']['keyspace_misses'],
            Cache::STATS_UPTIME => isset($stats['uptime_in_seconds']) ?
                $stats['uptime_in_seconds'] :
                $stats['Server']['uptime_in_seconds'],
            Cache::STATS_MEMORY_USAGE => isset($stats['used_memory']) ?
                $stats['used_memory'] :
                $stats['Memory']['used_memory'],
            Cache::STATS_MEMORY_AVAILIABLE => null,
        ];
    }
}
