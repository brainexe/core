<?php

namespace Matze\Core\MessageQueue;

interface MessageQueueWorkerInterface {
	/**
	 * @param integer $timeout
	 */
	public function run($timeout = 0);
}
