<?php

namespace Matze\Core\Authentication;

use Matze\Core\Traits\RedisTrait;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
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
	 * @return UserVO
	 */
	public function loadUserById($user_id) {
		$redis_user = $this->getPredis()->HGETALL($this->_getKey($user_id));

		$user = new UserVO();
		$user->id = $user_id;
		$user->username = $redis_user['username'];
		$user->password_hash = $redis_user['password'];
		$user->roles = array_filter(explode(',', $redis_user['roles']));

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
		return 'Matze\Core\Authentication\UserVO' === $class;
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
	 * @param UserVO $user
	 * @return integer $user_id
	 */
	public function register(UserVO $user) {
		$predis = $this->getPredis()->transaction();

		$user_array = [
			'username' => $user->getUsername(),
			'password' => $password_hash = $this->generateHash($user->password),
			'roles' => implode(',', $user->roles)
		];

		$new_user_id = mt_rand();

		$predis->HSET(self::REDIS_USER_NAMES, strtolower($user->getUsername()), $new_user_id);
		$predis->HMSET($this->_getKey($new_user_id), $user_array);

		$predis->execute();

		return $new_user_id;
	}
}
