<?php

namespace BrainExe\Core\MessageQueue;

interface MessageQueueWorkerInterface {
	/**
	 * @param integer $timeout (0 -> run forever, >0 -> live time in seconds)
	 * @param integer $interval (check interval in seconds)
	 */
	public function run($timeout = 0, $interval = 1);
}
