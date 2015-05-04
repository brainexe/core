<?php

namespace BrainExe\Core\Authentication;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Authentication\Event\DeleteUserEvent;
use BrainExe\Core\Authentication\Exception\UsernameNotFoundException;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\RedisTrait;
use BrainExe\Core\Redis\PhpRedis;

/**
 * @api
 * @Service(public=false)
 */
class DatabaseUserProvider
{

    use RedisTrait;
    use IdGeneratorTrait;
    use EventDispatcherTrait;

    const REDIS_USER       = 'user:%d';
    const REDIS_USER_NAMES = 'user_names';

    /**
     * @var PasswordHasher
     */
    private $passwordHasher;

    /**
     * @Inject({"@PasswordHasher"})
     * @param PasswordHasher $passwordHasher
     */
    public function __construct(PasswordHasher $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @throws UsernameNotFoundException
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
        $user->avatar          = isset($redisUser['avatar']) ? $redisUser['avatar'] : '';

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
        $hash           = $this->generateHash($newPassword);
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

        if (is_array($value)) {
            $value = implode(',', $value);
        }
        $redis->HSET($this->getKey($userVo->id), $property, $value);
    }

    /**
     * @param UserVO $user
     * @return integer $user_id
     */
    public function register(UserVO $user)
    {
        $redis        = $this->getRedis()->multi(PhpRedis::PIPELINE);
        $passwordHash = $this->generateHash($user->password);

        $userArray = [
            'username' => $user->getUsername(),
            'password' => $passwordHash,
            'roles' => implode(',', $user->roles),
            'one_time_secret' => $user->one_time_secret,
            'avatar' => $user->avatar
        ];

        $newUserId = $this->generateRandomNumericId();

        $redis->HSET(self::REDIS_USER_NAMES, strtolower($user->getUsername()), $newUserId);
        $redis->HMSET($this->getKey($newUserId), $userArray);

        $redis->execute();

        $user->id = $newUserId;

        return $newUserId;
    }

    /**
     * @param integer $userId
     */
    public function deleteUser($userId)
    {
        $redis = $this->getRedis();

        $user = $this->loadUserById($userId);

        $event = new DeleteUserEvent($user, DeleteUserEvent::DELETE);
        $this->dispatchEvent($event);

        $redis->hdel(self::REDIS_USER_NAMES, strtolower($user->getUsername()));
        $redis->del($this->getKey($userId));

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
