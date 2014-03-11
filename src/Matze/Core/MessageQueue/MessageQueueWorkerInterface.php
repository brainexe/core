<?php

namespace Matze\Core\MessageQueue;

interface MessageQueueWorkerInterface {
	/**
	 * @param integer $timeout
	 * @param integer $interval
	 */
	public function run($timeout = 0, $interval = 1);
}
