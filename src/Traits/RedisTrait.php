<?php

namespace BrainExe\Core\Traits;

use BrainExe\Core\Redis\Redis;
use BrainExe\Core\Redis\RedisInterface;

trait RedisTrait {

	/**
	 * @var Redis|RedisInterface
	 */
	private $_redis;

	/**
	 * @Inject("@Redis")
	 * @param Redis|RedisInterface $client
	 */
	public function setRedis(RedisInterface $client) {
		$this->_redis = $client;
	}

	/**
	 * @return Redis
	 */
	protected function getRedis() {
		return $this->_redis;
	}
}
