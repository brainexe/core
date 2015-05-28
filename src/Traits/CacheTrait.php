<?php

namespace BrainExe\Core\Traits;

use BrainExe\Annotations\Annotations\Inject;
use Doctrine\Common\Cache\CacheProvider;

/**
 * @api
 */
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
