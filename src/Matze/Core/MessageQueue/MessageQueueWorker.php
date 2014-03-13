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
			$got_lock = $this->_redis_lock->lock(self::MESSAGE_QUEUE_LOCK, self::LOCK_TIME);

			if (!$got_lock) {
				sleep(self::LOCK_TIME);

				continue;
			}

			$job = $this->_message_queue_gateway->fetchPendingEvent();
			if (empty($job)) {
				// wait for new job
				sleep($interval);
				$this->_redis_lock->unlock(self::MESSAGE_QUEUE_LOCK);
				continue;
			}

			$start = microtime(true);
			$this->dispatchEvent($job->event);
			$time = microtime(true) - $start;

			$this->_message_queue_gateway->deleteEvent($job->event_id);

			$this->_redis_lock->unlock(self::MESSAGE_QUEUE_LOCK);

			$this->info(sprintf('[MQ]: %s. Time: %0.2fms',
				$job->event->event_name, $time * 1000)
			);
		}


	}
} 
