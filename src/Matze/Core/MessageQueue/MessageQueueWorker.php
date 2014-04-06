<?php

namespace Matze\Core\MessageQueue;

use Matze\Core\Application\RedisLock;
use Matze\Core\Traits\EventDispatcherTrait;
use Matze\Core\Traits\LoggerTrait;
use Matze\Core\Traits\RedisTrait;
use Matze\Core\Traits\ServiceContainerTrait;

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
	 * @var RedisLock
	 */
	private $_redis_lock;

	/**
	 * @Inject({"@MessageQueueGateway", "@RedisLock"})
	 */
	public function __construct(MessageQueueGateway $message_queue_gateway, RedisLock $redis_lock) {
		$this->_message_queue_gateway = $message_queue_gateway;
		$this->_redis_lock = $redis_lock;
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
					$job->event->event_name, $needed_time * 1000)
				);
				$this->_message_queue_gateway->deleteEvent($job->event_id);
			} catch (\Exception $e) {
				$this->error($e->getMessage(), ['exception' => $e]);
				$this->_message_queue_gateway->restoreEvent($job->event_id);
			}
		}
	}
} 
