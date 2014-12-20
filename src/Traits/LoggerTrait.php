<?php

namespace BrainExe\Core\Traits;

use Monolog\Logger;

trait LoggerTrait
{

    use \Psr\Log\LoggerTrait;

    /**
     * @var Logger
     */
    private $_logger;

    /**
     * @Inject("@monolog.logger")
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->_logger = $logger;
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
        $this->_logger->log($level, $message, $context);
    }
}
