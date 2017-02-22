<?php

namespace BrainExe\Core\Traits;

use BrainExe\Core\Annotations\Inject;
use Monolog\Logger;

/**
 * @api
 */
trait LoggerTrait
{

    use \Psr\Log\LoggerTrait;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @Inject("@logger")
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = [])
    {
        $this->logger->log($level, $message, $context);
    }
}
