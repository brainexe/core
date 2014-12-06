<?php

namespace BrainExe\Core\Traits;

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
	public function wrapCache($key, $callback, $ttl = 3600) {
		$cached_value = $this->_redis->GET($key);
		if ($cached_value) {
			return unserialize($cached_value);
		}

		$value = $callback();

		$this->_redis->SETEX($key, $ttl, serialize($value));

		return $value;
	}

	/**
	 * @param string $key
	 */
	public function invalidate($key) {
		$this->_redis->DEL($key);
	}


}
