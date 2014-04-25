<?php

namespace Matze\Core\Authentication;

use Matze\Core\Traits\IdGeneratorTrait;
use Matze\Core\Traits\RedisTrait;

/**
 * @Service(public=false)
 */
class RegisterTokens {

	const TOKEN_KEY = 'register_tokens';

	use RedisTrait;
	use IdGeneratorTrait;

	/**
	 * @return string
	 */
	public function addToken() {
		$token = $this->generateRandomId();

		$this->getRedis()->sAdd(self::TOKEN_KEY, $token);

		return $token;
	}

	/**
	 * @param string $token
	 * @return boolean
	 */
	public function fetchToken($token) {
		return (bool)$this->getRedis()->sRem(self::TOKEN_KEY, $token);
	}
} 