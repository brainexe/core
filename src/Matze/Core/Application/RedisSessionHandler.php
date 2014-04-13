<?php

namespace Matze\Core\Application;

use Matze\Core\Traits\RedisTrait;

/**
 * @Service(public=false)
 */
class RedisSessionHandler implements \SessionHandlerInterface {

	const PREFIX = 'session:';

	use RedisTrait;

	/**
	 * {@inheritDoc}
	 */
	public function open($savePath, $sessionName) {
		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function close() {
	}

	/**
	 * {@inheritDoc}
	 */
	public function read($session_id) {
		$key = $this->_getKey($session_id);
		return $this->getRedis()->GET($key) ? : '';
	}

	/**
	 * {@inheritDoc}
	 */
	public function write($session_id, $data) {
		$key = $this->_getKey($session_id);

		$this->getRedis()->setex($key, 86400 * 7, $data);
	}

	/**
	 * {@inheritDoc}
	 */
	public function destroy($session_id) {
		return $this->getRedis()->DEL($this->_getKey($session_id));
	}

	/**
	 * {@inheritDoc}
	 */
	public function gc($lifetime) {
		return true;
	}

	/**
	 * @param string $session_id
	 * @return string
	 */
	private function _getKey($session_id) {
		return self::PREFIX . $session_id;
	}
}
