<?php

namespace BrainExe\Core\Traits;

use Doctrine\Common\Cache\CacheProvider;

trait CacheTrait
{

    /**
     * @var CacheProvider
     */
    private $cache;

    /**
     * @Inject("@Cache")
     * @param CacheProvider $cache
     */
    public function setCache(CacheProvider $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return CacheProvider
     */
    protected function getCache()
    {
        return $this->cache;
    }
}
