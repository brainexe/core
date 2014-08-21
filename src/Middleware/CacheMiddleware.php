<?php

namespace Matze\Core\Middleware;

use Matze\Core\Traits\CacheTrait;
use Matze\Core\Traits\LoggerTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

/**
 * @Middleware(priority=null)
 */
class CacheMiddleware extends AbstractMiddleware {

	use CacheTrait;
	use LoggerTrait;

	private $_cache_key;
	/**
	 * @var
	 */
	private $_cache_enabled;

	/**
	 * @Inject("%cache.enabled%")
	 */
	public function __construct($cache_enabled) {
		$this->_cache_enabled = $cache_enabled;
	}

	/**
	 * {@inheritdoc}
	 */
	public function processRequest(Request $request, Route $route, $route_name) {
		if (!$this->_cache_enabled || !$route->getOption('cache') || !$request->isMethod('GET')) {
			return null;
		}

		$this->_cache_key = $request->getRequestUri();

		$cache = $this->getCache();

		if ($cache->contains($this->_cache_key)) {
			$this->debug(sprintf('fetch from cache: %s', $this->_cache_key));

			$response = new Response($cache->fetch($this->_cache_key));
			$this->_cache_key = null;
			return $response;
		}

		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function processResponse(Request $request, Response $response) {
		if (!$this->_cache_key) {
			return null;
		}

		if (!$response->isOk()) {
			return;
		}

		$cache = $this->getCache();

		$this->debug(sprintf('save into cache: %s', $this->_cache_key));

		$cache->save($this->_cache_key, $response->getContent(), 60);
		$this->_cache_key = null;
	}
} 