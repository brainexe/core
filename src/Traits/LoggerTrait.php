<?php

namespace BrainExe\Core\Traits;

use Monolog\Logger;

trait LoggerTrait
{

    use \Psr\Log\LoggerTrait;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @Inject("@monolog.logger")
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
     * @return null
     */
    public function log($level, $message, array $context = [])
    {
        $this->logger->log($level, $message, $context);
    }
}
