<?php

namespace Matze\Core\Traits;

use Monolog\Logger;

trait LoggerTrait {

	use \Psr\Log\LoggerTrait;

	/**
	 * @var Logger
	 */
	private $_logger;

	/**
	 * @Inject("@monolog.logger")
	 */
	public function setLogger(Logger $logger) {
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
	public function log($level, $message, array $context = array()) {
		$this->_logger->log($level, $message, $context);
	}
} 
