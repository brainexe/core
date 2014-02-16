<?php

namespace Matze\Core\Traits;

use Predis\Client;

trait RedisTrait {

	/**
	 * @var Client
	 */
	private $_predis;

	/**
	 * @DI\Inject("@Predis")
	 */
	public function setPredis(Client $client) {
		$this->_predis = $client;
	}

	/**
	 * @return Client
	 */
	public function getPredis() {
		return $this->_predis;
	}
}