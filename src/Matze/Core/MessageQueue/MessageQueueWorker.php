<?php

namespace Matze\Core\MessageQueue;

use Matze\Core\EventDispatcher\AbstractEvent;
use Matze\Core\Traits\EventDispatcherTrait;
use Matze\Core\Traits\LoggerTrait;
use Matze\Core\Traits\RedisTrait;
use Matze\Core\Traits\ServiceContainerTrait;
use Raspberry\Radio\RadioChangeEvent;

/**
 * @Service(public=false)
 */
class MessageQueueWorker implements MessageQueueWorkerInterface {

	use LoggerTrait;
	use EventDispatcherTrait;

	/**
	 * @var MessageQueueGateway
	 */
	private $_message_queue_gateway;

	/**
	 * @Inject("@MessageQueueGateway")
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
			/** @var AbstractEvent[] $events */
			$events = $this->_message_queue_gateway->fetchPendingEvents();

			foreach ($events as $event) {
				$start = microtime(true);
				$this->dispatchEvent($event);
				$time = microtime(true) - $start;

				$this->info(sprintf('[MQ]: %s. Time: %0.2fms',
					$event->event_name, $time * 1000)
				);
			}

			sleep($interval);
		}
	}
} 
