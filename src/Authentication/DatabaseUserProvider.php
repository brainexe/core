<?php

namespace BrainExe\Core\Authentication;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Authentication\Event\DeleteUserEvent;
use BrainExe\Core\Authentication\Exception\UsernameNotFoundException;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\RedisTrait;

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
    private $hasher;

    /**
     * @var LoadUser
     */
    private $loadUser;

    /**
     * @Inject({
        "@PasswordHasher",
        "@Authentication.LoadUser",
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
     * @throws UsernameNotFoundException
     */
    public function loadUserByUsername($username)
    {
        return $this->loadUser->loadUserByUsername($username);
    }

    /**
     * @param integer $userId
     * @return UserVO
     */
    public function loadUserById($userId)
    {
        return $this->loadUser->loadUserById($userId);
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
        return $this->hasher->generateHash($password);
    }

    /**
     * @param string $password
     * @param string $hash
     * @return boolean
     */
    public function verifyHash($password, $hash)
    {
        return $this->hasher->verifyHash($password, $hash);
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
        $redis        = $this->getRedis()->pipeline();
        $passwordHash = $this->generateHash($user->password);

        $userArray = [
            'username' => $user->getUsername(),
            'password' => $passwordHash,
            'roles'    => implode(',', $user->roles),
            'one_time_secret' => $user->one_time_secret,
            'avatar'   => $user->avatar
        ];

        $newUserId = $this->generateUniqueId();

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
        $user = $this->loadUser->loadUserById($userId);

        if ($user instanceof AnonymusUserVO) {
            return;
        }

        $event = new DeleteUserEvent($user, DeleteUserEvent::DELETE);
        $this->dispatchEvent($event);

        $redis = $this->getRedis();
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
