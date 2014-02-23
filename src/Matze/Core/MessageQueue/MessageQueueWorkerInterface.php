<?php

namespace Matze\Core\MessageQueue;

interface MessageQueueWorkerInterface {
	/**
	 * Run worker
	 */
	public function run();
}