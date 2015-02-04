<?php

namespace BrainExe\Core\Redis;

use BrainExe\Core\Traits\LoggerTrait;
use BrainExe\Core\Redis\PhpRedis;
use Monolog\Logger;

/**
 * @service("Redis.Logger", public=false)
 */
class RedisLogger implements RedisInterface
{

    /**
     * @var PhpRedis
     */
    private $redis;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param PhpRedis $redis
     * @param Logger $logger
     */
    public function __construct(RedisInterface $redis, Logger $logger)
    {
        $this->redis  = $redis;
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

        $argumentList = $this->formatArguments($arguments);
        $log = sprintf("%0.2fms: %s %s", $diff * 1000, strtoupper($method), $argumentList);

        $this->logger->addDebug($log, ['channel' => 'redis']);

        return $result;
    }

    /**
     * @param array $arguments
     * @return string
     */
    private function formatArguments(array $arguments)
    {
        foreach ($arguments as &$argument) {
            if (is_array($argument)) {
                $argument = var_export($argument, true);
            }
        }

        return implode(', ', $arguments);
    }
}
