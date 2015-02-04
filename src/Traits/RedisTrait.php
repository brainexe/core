<?php

namespace BrainExe\Core\Traits;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Redis\PhpRedis;
use BrainExe\Core\Redis\Predis;
use BrainExe\Core\Redis\RedisInterface;

trait RedisTrait
{

    /**
     * @var PhpRedis|RedisInterface
     */
    private $redis;

    /**
     * @Inject("@Redis")
     * @param PhpRedis|Predis|RedisInterface $client
     */
    public function setRedis(RedisInterface $client)
    {
        $this->redis = $client;
    }

    /**
     * @return RedisInterface|PhpRedis|Predis
     */
    protected function getRedis()
    {
        return $this->redis;
    }
}
