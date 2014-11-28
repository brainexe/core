<?php

namespace BrainExe\Core\Authentication;

use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\RedisTrait;
use Redis;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @Service(public=false)
 */
class DatabaseUserProvider implements UserProviderInterface {

	use RedisTrait;
	use IdGeneratorTrait;

	const REDIS_USER       = 'user:%d';
	const REDIS_USER_NAMES = 'user_names';

	/**
	 * @var PasswordHasher
	 */
	private $passwordHasher;

	/**
	 * @inject({"@PasswordHasher"})
	 * @param PasswordHasher $passwordHasher
	 */
	public function __construct(PasswordHasher $passwordHasher) {
		$this->passwordHasher = $passwordHasher;
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadUserByUsername($username) {
		$user_id = $this->getRedis()->HGET(self::REDIS_USER_NAMES, strtolower($username));

		if (empty($user_id)) {
			throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
		}

		return $this->loadUserById($user_id);
	}

	/**
	 * @param integer $user_id
	 * @return UserVO
	 */
	public function loadUserById($user_id) {
		$redis_user = $this->getRedis()->HGETALL($this->_getKey($user_id));

		$user                  = new UserVO();
		$user->id              = $user_id;
		$user->username        = $redis_user['username'];
		$user->email           = isset($redis_user['email']) ? $redis_user['email'] : '';
		$user->password_hash   = $redis_user['password'];
		$user->one_time_secret = $redis_user['one_time_secret'];
		$user->roles           = array_filter(explode(',', $redis_user['roles']));

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
		return UserVO::class === $class;
	}

	/**
	 * @param string $password
	 * @return string $hash
	 */
	public function generateHash($password) {
		return $this->passwordHasher->generateHash($password);
	}

	/**
	 * @param string $password
	 * @param string $hash
	 * @return boolean
	 */
	public function verifyHash($password, $hash) {
		return $this->passwordHasher->verifyHash($password, $hash);
	}

	/**
	 * @param UserVO $user
	 * @param string $new_password
	 */
	public function changePassword(UserVO $user, $new_password) {
		$password_hash  = $this->generateHash($new_password);
		$user->password = $password_hash;

		$this->setUserProperty($user, 'password');
	}

	/**
	 * @param UserVO $user_vo
	 * @param string $property
	 */
	public function setUserProperty(UserVO $user_vo, $property) {
		$redis = $this->getRedis();

		$value = $user_vo->$property;
		$redis->HSET($this->_getKey($user_vo->id), $property, $value);
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
			'roles' => implode(',', $user->roles),
			'one_time_secret' => $user->one_time_secret
		];

		$new_user_id = $this->generateRandomNumericId();

		$redis->HSET(self::REDIS_USER_NAMES, strtolower($user->getUsername()), $new_user_id);
		$redis->HMSET($this->_getKey($new_user_id), $user_array);

		$redis->exec();

		$user->id = $new_user_id;

		return $new_user_id;
	}

	/**
	 * @param integer $user_id
	 * @return string
	 */
	private function _getKey($user_id) {
		return sprintf(self::REDIS_USER, $user_id);
	}

}
