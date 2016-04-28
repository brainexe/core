<?php

namespace BrainExe\Core\Middleware;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Middleware;
use BrainExe\Core\Traits\CacheTrait;
use BrainExe\Core\Traits\LoggerTrait;
use DateTime;
use Doctrine\Common\Cache\CacheProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

/**
 * @todo use X-Cache / X-Validate
 * @todo invalidate
 * @Middleware("Middleware.Cache")
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
     * @var bool
     */
    private $enabled;

    /**
     * @Inject("%cache.enabled%")
     * @param bool $cacheEnabled
     */
    public function __construct(bool $cacheEnabled)
    {
        $this->enabled = $cacheEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function processRequest(Request $request, Route $route)
    {
        if (!$this->enabled || !$route->getOption('cache') || !$request->isMethod('GET')) {
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
            return;
        }

        if (!$response->isOk()) {
            return;
        }

        $cache = $this->getCache();

        $this->debug(sprintf('save into cache: %s', $this->cacheKey));

        $cache->save($this->cacheKey, $response, $this->getTTL());
        $this->cacheKey = null;
    }

    /**
     * @param Request $request
     * @return string
     */
    private function generateCacheKey(Request $request) : string
    {
        return self::PREFIX . $request->getRequestUri();
    }

    /**
     * @param CacheProvider$cache
     * @return Response
     */
    private function handleCached(CacheProvider $cache) : Response
    {
        $this->debug(sprintf('fetch from cache: %s', $this->cacheKey));

        /** @var Response $response */
        $response       = $cache->fetch($this->cacheKey);
        $this->cacheKey = null;

        $ttl = $this->getTTL();

        $response->headers->set('X-Cache', 'hit');
        $response->setMaxAge($ttl);
        $response->setExpires(new DateTime(sprintf('+%d seconds', $ttl)));

        return $response;
    }

    /**
     * @return int
     */
    private function getTTL() : int
    {
        return self::DEFAULT_TTL;
    }
}
