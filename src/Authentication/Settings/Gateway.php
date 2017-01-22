<?php

namespace BrainExe\Core\Authentication\Settings;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Redis\Predis;

/**
 * @Service("User.Settings.Gateway", public=false)
 */
class Gateway
{

    const REDIS_KEY = 'user:settings:%s';

    /**
     * @var Predis
     */
    private $redis;

    /**
     * @Inject("@redis")
     * @param Predis $client
     */
    public function __construct(Predis $client)
    {
        $this->redis = $client;
    }

    /**
     * @param int $userId
     * @return string[]
     */
    public function getAll(int $userId) : array
    {
        return array_map('json_decode', $this->redis->hgetall($this->getKey($userId)));
    }

    /**
     * @param int $userId
     * @param string $setting
     * @return string
     */
    public function get(int $userId, string $setting)
    {
        return json_decode($this->redis->hget($this->getKey($userId), $setting));
    }

    /**
     * @param int $userId
     * @param string $setting
     * @param string $value
     */
    public function set(int $userId, string $setting, $value)
    {
        $this->redis->hset($this->getKey($userId), $setting, json_encode($value));
    }

    /**
     * @param int $userId
     * @return string
     */
    private function getKey(int $userId) : string
    {
        return sprintf(self::REDIS_KEY, $userId);
    }
}
