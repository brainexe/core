<?php

namespace BrainExe\Core\Authentication;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\RedisTrait;

/**
 * @Service("RegisterTokens", public=false)
 */
class RegisterTokens
{
    const TOKEN_KEY = 'register_tokens';

    use RedisTrait;
    use IdGeneratorTrait;

    /**
     * @return string
     */
    public function addToken() : string
    {
        $token = $this->generateRandomId();

        $this->getRedis()->sadd(self::TOKEN_KEY, [$token]);

        return $token;
    }

    /**
     * @param string $token
     * @return bool
     */
    public function fetchToken(string $token) : bool
    {
        return (bool)$this->getRedis()->srem(self::TOKEN_KEY, $token);
    }
}
