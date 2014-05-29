<?php

namespace Matze\Core\Traits;

use Doctrine\Common\Cache\CacheProvider;

trait CacheTrait {

	/**
	 * @var CacheProvider
	 */
	private $_cache;

	/**
	 * @Inject("@Cache")
	 */
	public function setCache(CacheProvider $cache) {
		$this->_cache = $cache;
	}

	/**
	 * @return CacheProvider
	 */
	protected function getCache() {
		return $this->_cache;
	}
}