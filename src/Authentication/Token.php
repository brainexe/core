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

    const USER_KEY = 'tokens:user:%s';
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
        $tokens    = $redis->hmget(self::TOKEN_KEY, $tokensIds);

        foreach ($tokens as $idx => $token) {
            yield $tokensIds[$idx] => json_decode($token, true);
        }
    }

    public function revokeToken($token)
    {

    }
}
