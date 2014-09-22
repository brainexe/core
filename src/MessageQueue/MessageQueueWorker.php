<?php

namespace BrainExe\Core\MessageQueue;

use BrainExe\Core\Application\RedisLock;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\LoggerTrait;

/**
 * @Service(public=false)
 */
class MessageQueueWorker implements MessageQueueWorkerInterface {

	use LoggerTrait;
	use EventDispatcherTrait;

	const MESSAGE_QUEUE_LOCK = 'message_queue_lock';
	const LOCK_TIME = 10;

	/**
	 * @var MessageQueueGateway
	 */
	private $_message_queue_gateway;

	/**
	 * @Inject({"@MessageQueueGateway"})
	 * @param MessageQueueGateway $message_queue_gateway
	 */
	public function __construct(MessageQueueGateway $message_queue_gateway) {
		$this->_message_queue_gateway = $message_queue_gateway;
	}

	/**
	 * {@inheritdoc}
	 */
	public function run($timeout = 0, $interval = 1) {
		$start = time();

		while ($timeout === 0 || $start + $timeout > time()) {
			$job = $this->_message_queue_gateway->fetchPendingEvent();
			if (empty($job)) {
				sleep($interval);
				continue;
			}
			try {
				$start = microtime(true);
				$this->dispatchEvent($job->event);
				$needed_time = microtime(true) - $start;

				$this->info(sprintf('[MQ]: %s. Time: %0.2fms',
					$job->event->event_name, $needed_time * 1000), ['channel' => 'message_queue']
				);
				$this->_message_queue_gateway->deleteEvent($job->event_id);
			} catch (\Exception $e) {
				$this->error($e->getMessage(), ['exception' => $e]);
				$this->_message_queue_gateway->restoreEvent($job->event_id);
			}
		}
	}
} 
