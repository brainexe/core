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
 * @todo X-Invalidate
 * @Middleware("Middleware.Cache")
 */
class Cache extends AbstractMiddleware
{
    use CacheTrait;
    use LoggerTrait;

    const DEFAULT_TTL = 60;
    const PREFIX = 'cache:';

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
        if (!$this->enabled || !$route->hasOption('cache') || !$request->isMethod('GET')) {
            return null;
        }

        $cacheKey = $this->generateCacheKey($request);

        $ttl   = $route->getOption('cache');
        $cache = $this->getCache();
        if ($cache->contains($cacheKey)) {
            return $this->handleCached($cache, $cacheKey, $ttl);
        }

        // enable cache for current request. Store response later in given key
        $request->attributes->set('_cacheKey', $cacheKey);
        $request->attributes->set('_cacheTTL', $ttl);

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function processResponse(Request $request, Response $response)
    {
        if (!$response->isOk()) {
            return;
        }

        $cacheKey = $request->attributes->get('_cacheKey');
        $ttl      = $request->attributes->get('_cacheTTL');
        if (empty($cacheKey)) {
            return;
        }

        $cache = $this->getCache();

        $this->info(sprintf('miss: save into cache: %s', $cacheKey), [
            'application' => 'cache',
            'type'        => 'miss',
            'cacheKey'    => $cacheKey,
            'ttl'         => $ttl,
        ]);

        $cache->save($cacheKey, $response, $ttl);

        $response->headers->set('X-Cache', 'miss');
        $response->setMaxAge($ttl);
        $response->setExpires(new DateTime(sprintf('+%d seconds', $ttl)));
    }

    /**
     * @param CacheProvider $cache
     * @param string $cacheKey
     * @param int $ttl
     * @return Response
     */
    private function handleCached(CacheProvider $cache, string $cacheKey, int $ttl) : Response
    {
        $this->info(sprintf('hit: fetch from cache: %s', $cacheKey), [
            'application' => 'cache',
            'type'        => 'hit',
            'cacheKey'    => $cacheKey,
            'ttl'         => $ttl,
        ]);

        /** @var Response $response */
        $response = $cache->fetch($cacheKey);

        $response->headers->set('X-Cache', 'hit');
        $response->setMaxAge($ttl);
        $response->setExpires(new DateTime(sprintf('+%d seconds', $ttl)));

        return $response;
    }

    /**
     * @param Request $request
     * @return string
     */
    private function generateCacheKey(Request $request) : string
    {
        return self::PREFIX . $request->getRequestUri();
    }
}
