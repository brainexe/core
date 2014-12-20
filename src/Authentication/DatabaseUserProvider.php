<?php

namespace BrainExe\Core\Authentication;

use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\RedisTrait;
use BrainExe\Core\Redis\Redis;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @Service(public=false)
 */
class DatabaseUserProvider implements UserProviderInterface
{

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
    public function __construct(PasswordHasher $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        $userId = $this->getRedis()->HGET(self::REDIS_USER_NAMES, strtolower($username));

        if (empty($userId)) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        return $this->loadUserById($userId);
    }

    /**
     * @param integer $userId
     * @return UserVO
     */
    public function loadUserById($userId)
    {
        $redisUser = $this->getRedis()->HGETALL($this->getKey($userId));

        $user                  = new UserVO();
        $user->id              = $userId;
        $user->username        = $redisUser['username'];
        $user->email           = isset($redisUser['email']) ? $redisUser['email'] : '';
        $user->password_hash   = $redisUser['password'];
        $user->one_time_secret = $redisUser['one_time_secret'];
        $user->roles           = array_filter(explode(',', $redisUser['roles']));

        return $user;
    }

    /**
     * @return string[]
     */
    public function getAllUserNames()
    {
        return $this->getRedis()->hGetAll(self::REDIS_USER_NAMES);
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return UserVO::class === $class;
    }

    /**
     * @param string $password
     * @return string $hash
     */
    public function generateHash($password)
    {
        return $this->passwordHasher->generateHash($password);
    }

    /**
     * @param string $password
     * @param string $hash
     * @return boolean
     */
    public function verifyHash($password, $hash)
    {
        return $this->passwordHasher->verifyHash($password, $hash);
    }

    /**
     * @param UserVO $user
     * @param string $newPassword
     */
    public function changePassword(UserVO $user, $newPassword)
    {
        $hash  = $this->generateHash($newPassword);
        $user->password = $hash;

        $this->setUserProperty($user, 'password');
    }

    /**
     * @param UserVO $userVo
     * @param string $property
     */
    public function setUserProperty(UserVO $userVo, $property)
    {
        $redis = $this->getRedis();

        $value = $userVo->$property;
        $redis->HSET($this->getKey($userVo->id), $property, $value);
    }

    /**
     * @param UserVO $user
     * @return integer $user_id
     */
    public function register(UserVO $user)
    {
        $redis        = $this->getRedis()->multi(Redis::PIPELINE);
        $passwordHash = $this->generateHash($user->password);

        $userArray = [
            'username' => $user->getUsername(),
            'password' => $passwordHash,
            'roles' => implode(',', $user->roles),
            'one_time_secret' => $user->one_time_secret
        ];

        $newUserId = $this->generateRandomNumericId();

        $redis->HSET(self::REDIS_USER_NAMES, strtolower($user->getUsername()), $newUserId);
        $redis->HMSET($this->getKey($newUserId), $userArray);

        $redis->exec();

        $user->id = $newUserId;

        return $newUserId;
    }

    /**
     * @param integer $userId
     * @return string
     */
    private function getKey($userId)
    {
        return sprintf(self::REDIS_USER, $userId);
    }
}
