<?php

namespace Matze\Core\Application;
use Matze\Core\Traits\RedisTrait;

/**
 * @Service(public=false)
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
		$predis = $this->getPredis();

		$result = $predis->SETNX($name, $now + $lock_time);
		if ($result) {
			return true;
		}

		$lock_time = $predis->GET($name);
		if ($now > $lock_time) {
			$predis->SET($name, $now + $lock_time);
			return true;
		}

		return false;
	}

	/**
	 * @param string $name
	 */
	public function unlock($name) {
		$this->getPredis()->DEL($name);
	}
} 