<?php

namespace Matze\Core\Authentication;

use Matze\Core\Traits\PDOTrait;
use Matze\Core\Traits\RedisTrait;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @Service("Security.UserProvider", public=false)
 */
class DatabaseUserProvider implements UserProviderInterface {

	use RedisTrait;

	const REDIS_USER = 'user:%d';
	const REDIS_USER_NAMES = 'user_names';

	/**
	 * {@inheritdoc}
	 */
	public function loadUserByUsername($username) {
		$predis = $this->getPredis();

		$user_id = $predis->HGET(self::REDIS_USER_NAMES, strtolower($username));
		if (!$user_id) {
			throw new UsernameNotFoundException(
				sprintf('Username "%s" does not exist.', $username)
			);
		}

		$redis_user = $predis->HGETALL($this->_getKey($user_id));

		$user = new User($redis_user['username'], $redis_user['password'], explode(',', $redis_user['roles']));

		return new $user;
	}

	/**
	 * {@inheritdoc}
	 */
	public function refreshUser(UserInterface $user) {
		return $this->loadUserByUsername($user->getUsername());
	}

	/**
	 * {@inheritdoc}
	 */
	public function supportsClass($class) {
		return 'Symfony\Component\Security\Core\User\User' === $class;
	}

	/**
	 * @param integer $user_id
	 * @return string
	 */
	private function _getKey($user_id) {
		return sprintf(self::REDIS_USER, $user_id);
	}
}
