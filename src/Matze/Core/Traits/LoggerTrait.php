<?php

namespace Matze\Core\Traits;

use Matze\Annotations\Annotations as DI;
use Monolog\Logger;

trait LoggerTrait {

	use \Psr\Log\LoggerTrait;

	/**
	 * @var Logger
	 */
	private $_logger;

	/**
	 * @return Logger
	 */
	public function getLogger() {
		return $this->_logger;
	}

	/**
	 * @Inject("@monolog.logger")
	 */
	public function setLogger(Logger $pdo) {
		$this->_logger = $pdo;
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