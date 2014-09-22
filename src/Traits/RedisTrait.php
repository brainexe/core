<?php

namespace BrainExe\Core\Traits;

use Redis;

trait RedisTrait {

	/**
	 * @var Redis
	 */
	private $_redis;

	/**
	 * @Inject("@Redis")
	 */
	public function setRedis(Redis $client) {
		$this->_redis = $client;
	}

	/**
	 * @return Redis
	 */
	protected function getRedis() {
		return $this->_redis;
	}
}