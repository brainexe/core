<?php

namespace BrainExe\Core\Authentication;

use BrainExe\Core\Annotations\Service;
use BrainExe\Core\Authentication\Exception\UserNotFoundException;
use BrainExe\Core\Redis\Predis;

/**
 * @api
 * @Service
 */
class LoadUser
{
    /**
     * @var Predis
     */
    private $redis;

    /**
     * @param Predis $redis
     */
    public function __construct(Predis $redis)
    {
        $this->redis = $redis;
    }

    /**
     * @param string $username
     * @return UserVO
     * @throws UserNotFoundException
     */
    public function loadUserByUsername(string $username) : UserVO
    {
        $userId = $this->redis->hget(UserProvider::REDIS_USER_NAMES, mb_strtolower($username));

        if (empty($userId)) {
            throw new UserNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        return $this->loadUserById($userId);
    }

    /**
     * @param int $userId
     * @return UserVO
     * @throws UserNotFoundException
     */
    public function loadUserById(int $userId) : UserVO
    {
        $redisUser = $this->redis->hgetall($this->getKey($userId));

        if (empty($redisUser)) {
            throw new UserNotFoundException(sprintf('User "%d" does not exist.', $userId));
        }

        return $this->buildUserVO($userId, $redisUser);
    }

    /**
     * @param int $userId
     * @return string
     */
    private function getKey(int $userId) : string
    {
        return sprintf(UserProvider::REDIS_USER, $userId);
    }

    /**
     * @param int $userId
     * @param $redisUser
     * @return UserVO
     */
    private function buildUserVO(int $userId, array $redisUser) : UserVO
    {
        $user                  = new UserVO();
        $user->id              = $userId;
        $user->username        = $redisUser['username'];
        $user->email           = $redisUser['email'] ?? '';
        $user->password_hash   = $redisUser['password'];
        $user->one_time_secret = $redisUser['one_time_secret'] ?? null;
        $user->roles           = array_filter(explode(',', $redisUser['roles']));
        $user->avatar          = $redisUser['avatar'] ?? UserVO::DEFAULT_AVATAR;

        return $user;
    }
}
