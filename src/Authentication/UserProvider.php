<?php

namespace BrainExe\Core\Authentication;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Authentication\Event\DeleteUserEvent;
use BrainExe\Core\Authentication\Exception\UserNotFoundException;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\RedisTrait;

/**
 * @api
 * @Service("Core.Authentication.UserProvider", public=false)
 */
class UserProvider
{

    use RedisTrait;
    use IdGeneratorTrait;
    use EventDispatcherTrait;

    const REDIS_USER       = 'user:%d';
    const REDIS_USER_NAMES = 'user_names';

    /**
     * @var PasswordHasher
     */
    private $hasher;

    /**
     * @var LoadUser
     */
    private $loadUser;

    /**
     * @Inject({
        "@Core.Authentication.PasswordHasher",
        "@Core.Authentication.LoadUser",
     * })
     * @param PasswordHasher $passwordHasher
     * @param LoadUser $loadUser
     */
    public function __construct(PasswordHasher $passwordHasher, LoadUser $loadUser)
    {
        $this->hasher   = $passwordHasher;
        $this->loadUser = $loadUser;
    }

    /**
     * @param string $username
     * @return UserVO
     * @throws UserNotFoundException
     */
    public function loadUserByUsername(string $username) : UserVO
    {
        return $this->loadUser->loadUserByUsername($username);
    }

    /**
     * @param int $userId
     * @return UserVO
     * @throws UserNotFoundException
     */
    public function loadUserById(int $userId) : UserVO
    {
        return $this->loadUser->loadUserById($userId);
    }

    /**
     * @return string[]
     */
    public function getAllUserNames() : array
    {
        return $this->getRedis()->hgetall(self::REDIS_USER_NAMES);
    }

    /**
     * @param string $password
     * @return string $hash
     */
    public function generateHash(string $password) : string
    {
        return $this->hasher->generateHash($password);
    }

    /**
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function verifyHash(string $password, string $hash) : bool
    {
        return $this->hasher->verifyHash($password, $hash);
    }

    /**
     * @param UserVO $user
     * @param string $newPassword
     */
    public function changePassword(UserVO $user, string $newPassword)
    {
        $hash = $this->generateHash($newPassword);
        $user->password = $hash;

        $this->setUserProperty($user, 'password');
    }

    /**
     * @param UserVO $userVo
     * @param string $property
     */
    public function setUserProperty(UserVO $userVo, string $property)
    {
        $redis = $this->getRedis();
        $value = $userVo->$property;

        if (is_array($value)) {
            $value = implode(',', $value);
        }
        $redis->hset($this->getKey($userVo->id), $property, $value);
    }

    /**
     * @param UserVO $user
     * @return int $userId
     */
    public function register(UserVO $user) : int
    {
        $redis = $this->getRedis()->pipeline();

        $userArray = [
            'username' => $user->getUsername(),
            'password' => $this->generateHash($user->password),
            'roles'    => implode(',', $user->roles),
            'one_time_secret' => $user->one_time_secret,
            'avatar'   => $user->avatar
        ];

        $newUserId = $this->generateUniqueId('userid');

        $redis->hset(self::REDIS_USER_NAMES, mb_strtolower($user->getUsername()), $newUserId);
        $redis->hmset($this->getKey($newUserId), $userArray);

        $redis->execute();

        $user->id = $newUserId;

        return $newUserId;
    }

    /**
     * @param int $userId
     * @return bool
     */
    public function deleteUser(int $userId) : bool
    {
        try {
            $user = $this->loadUser->loadUserById($userId);
        } catch (UserNotFoundException $e) {
            return false;
        }

        $event = new DeleteUserEvent($user, DeleteUserEvent::DELETE);
        $this->dispatchEvent($event);

        $redis = $this->getRedis();
        $redis->hdel(self::REDIS_USER_NAMES, mb_strtolower($user->getUsername()));
        $redis->del($this->getKey($userId));

        return true;
    }

    /**
     * @param int $userId
     * @return string
     */
    private function getKey(int $userId) : string
    {
        return sprintf(self::REDIS_USER, $userId);
    }
}
