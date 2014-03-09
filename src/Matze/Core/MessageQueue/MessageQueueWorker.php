<?php

namespace Matze\Core\MessageQueue;

use Matze\Core\Traits\LoggerTrait;
use Matze\Core\Traits\RedisTrait;
use Matze\Core\Traits\ServiceContainerTrait;

/**
 * @Service(public=false)
 */
class MessageQueueWorker implements MessageQueueWorkerInterface {

	use ServiceContainerTrait;
	use LoggerTrait;

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
	public function run($timeout = 0) {
		$start = time();

		while ($timeout === 0 || $start + $timeout > time()) {
			$events = $this->_message_queue_gateway->fetchPendingEvents();

			foreach ($events as $event) {
				$service = $this->getService($event->service_id);

				$start = microtime(true);
				// todo dispatch event
				call_user_func_array([$service, $event->method], $event->arguments);
				$time = microtime(true) - $start;

//				$this->info(sprintf('[MQ]: %s->%s(%s). Time: %0.2fms',
//					$event->service_id, $event->method, implode(', ', $event->arguments), $time * 1000)
//				);
			}

			sleep(1);
		}
	}
} 
