<?php

namespace BrainExe\Core\Redis;

use BrainExe\Core\Traits\LoggerTrait;
use BrainExe\Core\Redis\Redis;
use Monolog\Logger;

/**
 * @service("Redis.Logger", public=false)
 */
class RedisLogger implements RedisInterface
{

    /**
     * @var Redis
     */
    private $redis;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param Redis $redis
     * @param Logger $logger
     */
    public function __construct(Redis $redis, Logger $logger)
    {
        $this->redis = $redis;
        $this->logger = $logger;
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call($method, array $arguments)
    {
        $start = microtime(true);

        $result = call_user_func_array([$this->redis, $method], $arguments);

        $diff = microtime(true) - $start;

        $argumentList = var_export($arguments, true);
        $log = sprintf("%0.2fms: %s %s", $diff * 1000, strtoupper($method), $argumentList);

        $this->logger->addDebug($log, ['channel' => 'redis']);

        return $result;
    }
}
