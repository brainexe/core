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
		while (true) {
			$event = $this->_message_queue_gateway->waitForNewJob($timeout);

			if (empty($event)) {
				break;
			}

			$service = $this->getService($event->service_id);

			$start = microtime(true);
			call_user_func_array([$service, $event->method], $event->arguments);
			$time = microtime(true) - $start;

			$this->info(sprintf('[MQ]: %s->%s(%s). Time: %0.2fms',
					$event->service_id, $event->method, implode(', ', $event->arguments), $time * 1000)
			);
		}
	}
} 
