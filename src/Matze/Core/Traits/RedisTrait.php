<?php

namespace Matze\Core\Traits;

use Predis\Client;

trait RedisTrait {

	/**
	 * @var Client
	 */
	private $_predis;

	/**
	 * @Inject("@Predis")
	 */
	public function setPredis(Client $client) {
		$this->_predis = $client;
	}

	/**
	 * @return Client
	 */
	protected function getRedis() {
		return $this->_predis;
	}
}