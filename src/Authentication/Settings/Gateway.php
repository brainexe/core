<?php

namespace BrainExe\Core\Authentication\Settings;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\RedisTrait;

/**
 * @Service("User.Settings.Gateway", public=false)
 */
class Gateway
{

    const REDIS_KEY = 'user:settings:%s';

    use RedisTrait;

    /**
     * @param int $userId
     * @return string[]
     */
    public function getAll($userId)
    {
        return array_map('json_decode', $this->getRedis()->hgetall($this->getKey($userId)));
    }

    /**
     * @param int $userId
     * @param string $setting
     * @return string
     */
    public function get($userId, $setting)
    {
        return json_decode($this->getRedis()->hget($this->getKey($userId), $setting));
    }

    /**
     * @param int $userId
     * @param string $setting
     * @param string $value
     */
    public function set($userId, $setting, $value)
    {
        $this->getRedis()->hset($this->getKey($userId), $setting, json_encode($value));
    }


    /**
     * @param int $userId
     * @return string
     */
    private function getKey($userId)
    {
        return sprintf(self::REDIS_KEY, $userId);
    }
}
