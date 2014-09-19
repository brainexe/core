<?php

namespace Matze\Core\Application;

use Matze\Core\Traits\RedisTrait;

/**
 * @Service(public=false)
 * @todo improve locking
 */
class RedisLock {

	use RedisTrait;

	/**
	 * @param string $name
	 * @param integer $lock_time
	 * @return boolean $got_lock
	 */
	public function lock($name, $lock_time) {
		$now = time();
		$redis = $this->getRedis();

		$result = $redis->SETNX($name, $now + $lock_time);
		if ($result) {
			return true;
		}

		$lock_time = $redis->GET($name);
		if ($now > $lock_time) {
			$redis->SET($name, $now + $lock_time);
			return true;
		}

		return false;
	}

	/**
	 * @param string $name
	 */
	public function unlock($name) {
		$this->getRedis()->DEL($name);
	}
} 