<?php

namespace Matze\Core\Traits;

/**
 * TODO use Cache object / refactor
 */
trait RedisCacheTrait {

	use RedisTrait;

	/**
	 * @param string $key
	 * @param callable $callback
	 * @param integer $ttl
	 * @return mixed
	 */
	protected function wrapCache($key, $callback, $ttl = 3600) {
		$cached_value = $this->_redis->GET($key);
		if ($cached_value) {
			return unserialize($cached_value);
		}

		$value = $callback();

		$this->_redis->SET($key, serialize($value));
		$this->_redis->EXPIRE($key, $ttl);

		return $value;
	}

	/**
	 * @param string $key
	 */
	protected function invalidate($key) {
		$this->_redis->DEL($key);
	}


}
