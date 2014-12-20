<?php

namespace BrainExe\Core\Middleware;

use BrainExe\Core\Traits\CacheTrait;
use BrainExe\Core\Traits\LoggerTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

/**
 * @todo use X-Cache / X-Validate
 * @todo invalidate
 * @Middleware(priority=null)
 */
class CacheMiddleware extends AbstractMiddleware
{

    use CacheTrait;
    use LoggerTrait;

    const TTL = 60;

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
    public function processRequest(Request $request, Route $route, $routeName)
    {
        if (!$this->cacheEnabled || !$route->getOption('cache') || !$request->isMethod('GET')) {
            return null;
        }

        $this->cacheKey = $request->getRequestUri();

        $cache = $this->getCache();

        if ($cache->contains($this->cacheKey)) {
            $this->debug(sprintf('fetch from cache: %s', $this->cacheKey));

            $response = $cache->fetch($this->cacheKey);
            $this->cacheKey = null;

            return $response;
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

        $cache->save($this->cacheKey, $response, self::TTL);
        $this->cacheKey = null;
    }
}
