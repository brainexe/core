<?php

namespace Matze\Core\Authentication;

use Matze\Core\Traits\PDOTrait;
use Matze\Core\Traits\RedisTrait;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @Service("DatabaseUserProvider", public=false)
 */
class DatabaseUserProvider implements UserProviderInterface {

	use RedisTrait;

	const REDIS_USER = 'user:%d';
	const REDIS_USER_NAMES = 'user_names';

	/**
	 * {@inheritdoc}
	 */
	public function loadUserByUsername($username) {
		$user_id = $this->getPredis()->HGET(self::REDIS_USER_NAMES, strtolower($username));

		if (empty($user_id)) {
			throw new UsernameNotFoundException(
				sprintf('Username "%s" does not exist.', $username)
			);
		}

		return $this->loadUserById($user_id);
	}

	/**
	 * @param integer $user_id
	 * @return User
	 */
	public function loadUserById($user_id) {
		$redis_user = $this->getPredis()->HGETALL($this->_getKey($user_id));

		print_r($redis_user);
		print_r($redis_user['username']);

		$user = new User($redis_user['username'], $redis_user['password'], explode(',', $redis_user['roles']));

		return $user;
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

	/**
	 * @param string $password
	 * @return string
	 * @todo replace by password_hash()
	 */
	public function generateHash($password) {
		return sha1($password.md5($password));
	}

	/**
	 * @param User $user
	 * @return integer $user_id
	 */
	public function register(User $user) {
		$predis = $this->getPredis()->transaction();

		$user_array = [
			'username' => $user->getUsername(),
			'password' => $password_hash = $this->generateHash($user->getPassword()),
			'roles' => implode(',', $user->getRoles())
		];

		$user_id = mt_rand(1000, 10000000); //TODO

		$predis->HSET(self::REDIS_USER_NAMES, strtolower($user->getUsername()), $user_id);
		$predis->HMSET($this->_getKey($user_id), $user_array);

		$predis->execute();

		return $user_id;
	}
}
