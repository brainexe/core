<?php

namespace BrainExe\Core\Authentication;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Authentication\Exception\UsernameNotFoundException;
use Predis\Client;

/**
 * @api
 * @Service("Authentication.LoadUser", public=false)
 */
class LoadUser
{
    /**
     * @var Client
     */
    private $redis;

    /**
     * @param Client $redis
     * @Inject("@redis")
     */
    public function __construct(Client $redis)
    {
        $this->redis = $redis;
    }

    /**
     * @param string $username
     * @return UserVO
     * @throws UsernameNotFoundException
     */
    public function loadUserByUsername($username)
    {
        $userId = $this->redis->hget(UserProvider::REDIS_USER_NAMES, strtolower($username));

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
        $redisUser = $this->redis->hgetall($this->getKey($userId));

        if (empty($redisUser)) {
            return new AnonymusUserVO();
        }

        $user                  = new UserVO();
        $user->id              = $userId;
        $user->username        = $redisUser['username'];
        $user->email           = isset($redisUser['email']) ? $redisUser['email'] : '';
        $user->password_hash   = $redisUser['password'];
        $user->one_time_secret = isset($redisUser['one_time_secret']) ? $redisUser['one_time_secret'] : null;
        $user->roles           = array_filter(explode(',', $redisUser['roles']));
        $user->avatar          = isset($redisUser['avatar']) ? $redisUser['avatar'] : UserVO::AVATAR_5;

        return $user;
    }

    /**
     * @param integer $userId
     * @return string
     */
    private function getKey($userId)
    {
        return sprintf(UserProvider::REDIS_USER, $userId);
    }
}
