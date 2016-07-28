<?php

namespace BrainExe\Core\Traits;

use BrainExe\Annotations\Annotations\Inject;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @api
 */
trait CacheTrait
{

    /**
     * @var AdapterInterface
     */
    private $cache;

    /**
     * @Inject("@Cache")
     * @param AdapterInterface $cache
     */
    public function setCache(AdapterInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return AdapterInterface
     */
    protected function getCache() : AdapterInterface
    {
        return $this->cache;
    }
}
