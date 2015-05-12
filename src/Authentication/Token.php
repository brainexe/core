<?php

namespace BrainExe\Core\Authentication;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\RedisTrait;
use Generator;

/**
 * @Service("Authentication.Token", public=false)
 */
class Token
{

    const USER_KEY  = 'tokens:user:%s';
    const TOKEN_KEY = 'tokens';

    use RedisTrait;
    use IdGeneratorTrait;

    /**
     * @param int $userId
     * @param string[] $roles
     * @return string
     */
    public function addToken($userId, array $roles = [])
    {
        $token = $this->generateRandomId(32);

        $redis = $this->getRedis()->pipeline();

        $redis->sadd(sprintf(self::USER_KEY, $userId), $token);
        $redis->hset(self::TOKEN_KEY, $token, json_encode(['userId' => $userId, 'roles' => $roles]));

        $redis->execute();

        return $token;
    }

    /**
     * @param string $token
     * @return array
     */
    public function getToken($token)
    {
        return json_decode($this->getRedis()->hget(self::TOKEN_KEY, $token), true);
    }

    /**
     * @param int $userId
     * @return array[]|Generator
     */
    public function getTokensForUser($userId)
    {
        $redis     = $this->getRedis();
        $tokensIds = $redis->smembers(sprintf(self::USER_KEY, $userId));

        if (!empty($tokensIds)) {
            $tokens = $redis->hmget(self::TOKEN_KEY, $tokensIds);

            foreach ($tokens as $idx => $token) {
                yield $tokensIds[$idx] => json_decode($token, true)['roles'];
            }
        }
    }

    /**
     * @param string $token
     * @param string|null $role
     * @return int
     */
    public function hasUserForRole($token, $role = null)
    {
        $tokenData = $this->getToken($token);
        if (empty($tokenData)) {
            return null;
        }

        if ($role && !in_array($role, $tokenData['roles'])) {
            return null;
        }

        return $tokenData['userId'];
    }

    /**
     * @param string $token
     */
    public function revoke($token)
    {
        $tokenData = $this->getToken($token);
        if (!$tokenData) {
            return;
        }

        $userId = $tokenData['userId'];

        $redis = $this->getRedis()->pipeline();

        $redis->srem(sprintf(self::USER_KEY, $userId), $token);
        $redis->hdel(self::TOKEN_KEY, $token);

        $redis->execute();
    }
}
