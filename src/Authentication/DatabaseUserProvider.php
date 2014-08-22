<?php

namespace Matze\Core\Authentication;

use Matze\Core\Traits\RedisTrait;
use Redis;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @Service(public=true)
 */
class DatabaseUserProvider implements UserProviderInterface {

	use RedisTrait;

	const REDIS_USER = 'user:%d';
	const REDIS_USER_NAMES = 'user_names';

	/**
	 * {@inheritdoc}
	 */
	public function loadUserByUsername($username) {
		$user_id = $this->getRedis()->HGET(self::REDIS_USER_NAMES, strtolower($username));

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
		$redis_user = $this->getRedis()->HGETALL($this->_getKey($user_id));

		$user = new UserVO();
		$user->id = $user_id;
		$user->username = $redis_user['username'];
		$user->email = isset($redis_user['email']) ? $redis_user['email'] : '';
		$user->password_hash = $redis_user['password'];
		$user->roles = array_filter(explode(',', $redis_user['roles']));

		return $user;
	}

	/**
	 * @return string[]
	 */
	public function getAllUserNames() {
		return $this->getRedis()->hGetAll(self::REDIS_USER_NAMES);
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
	 * @return string $hash
	 */
	public function generateHash($password) {
		return password_hash($password, PASSWORD_BCRYPT);
	}

	/**
	 * @param string $password
	 * @param string $hash
	 * @return boolean
	 */
	public function verifyHash($password, $hash) {
		return password_verify($password, $hash);
	}

	/**
	 * @param integer $user_id
	 * @param string $new_password
	 */
	public function changePassword($user_id, $new_password) {
		$redis = $this->getRedis();

		$password_hash = $this->generateHash($new_password);

		$redis->HSET($this->_getKey($user_id), 'password', $password_hash);
	}

	/**
	 * @param UserVO $user
	 * @return integer $user_id
	 */
	public function register(UserVO $user) {
		$redis = $this->getRedis()->multi(Redis::PIPELINE);

		$user_array = [
			'username' => $user->getUsername(),
			'password' => $password_hash = $this->generateHash($user->password),
			'roles' => implode(',', $user->roles)
		];

		$new_user_id = mt_rand();

		$redis->HSET(self::REDIS_USER_NAMES, strtolower($user->getUsername()), $new_user_id);
		$redis->HMSET($this->_getKey($new_user_id), $user_array);

		$redis->exec();

		$user->id = $new_user_id;

		return $new_user_id;
	}
}
