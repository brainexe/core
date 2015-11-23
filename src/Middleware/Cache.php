<?php

namespace BrainExe\Core\Middleware;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Middleware;
use BrainExe\Core\Traits\CacheTrait;
use BrainExe\Core\Traits\LoggerTrait;
use Doctrine\Common\Cache\CacheProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

/**
 * @todo use X-Cache / X-Validate
 * @todo invalidate
 * @Middleware("Middleware.Cache", priority=null)
 */
class Cache extends AbstractMiddleware
{

    use CacheTrait;
    use LoggerTrait;

    const DEFAULT_TTL = 60;
    const PREFIX = 'cache:';

    /**
     * @var string
     */
    private $cacheKey;

    /**
     * @var boolean
     */
    private $cacheEnabled;

    /**
     * @Inject("%cache.enabled%")
     * @param boolean $cacheEnabled
     */
    public function __construct($cacheEnabled)
    {
        $this->cacheEnabled = $cacheEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function processRequest(Request $request, Route $route)
    {
        if (!$this->cacheEnabled || !$route->getOption('cache') || !$request->isMethod('GET')) {
            return null;
        }

        $this->cacheKey = $this->generateCacheKey($request);

        $cache = $this->getCache();

        if ($cache->contains($this->cacheKey)) {
            return $this->handleCached($cache);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function processResponse(Request $request, Response $response)
    {
        if (!$this->cacheKey) {
            return null;
        }

        if (!$response->isOk()) {
            return;
        }

        $cache = $this->getCache();

        $this->debug(sprintf('save into cache: %s', $this->cacheKey));

        $cache->save($this->cacheKey, $response, self::DEFAULT_TTL);
        $this->cacheKey = null;
    }

    /**
     * @param Request $request
     * @return string
     */
    private function generateCacheKey(Request $request)
    {
        return self::PREFIX . $request->getRequestUri();
    }

    /**
     * @param CacheProvider$cache
     * @return Response
     */
    protected function handleCached(CacheProvider $cache)
    {
        $this->debug(sprintf('fetch from cache: %s', $this->cacheKey));

        /** @var Response $response */
        $response       = $cache->fetch($this->cacheKey);
        $this->cacheKey = null;

        $response->headers->set('X-Cache', 'hit');

        return $response;
    }
}
