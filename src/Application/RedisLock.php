<?php

namespace BrainExe\Core\Application;

use BrainExe\Core\Traits\RedisTrait;
use BrainExe\Core\Traits\TimeTrait;

/**
 * @Service(public=false)
 */
class RedisLock {
	const REDIS_PREFIX = 'lock:';

	use RedisTrait;

	/**
	 * @param string $name
	 * @param integer $lock_time
	 * @return boolean $got_lock
	 */
	public function lock($name, $lock_time) {
		$redis = $this->getRedis();

		$exists = $redis->EXISTS(self::REDIS_PREFIX . $name);
		if (!$exists) {
			$redis->SETEX($name, $lock_time, 1);
			return true;
		}

		return false;
	}

	/**
	 * @param string $name
	 */
	public function unlock($name) {
		$this->getRedis()->DEL(self::REDIS_PREFIX . $name);
	}
} 