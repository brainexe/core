<?php

namespace Matze\Core\MessageQueue;

use Matze\Core\Traits\RedisTrait;
use Matze\Core\Traits\ServiceContainerTrait;
use Matze\Annotations\Annotations as DI;

/**
 * @DI\Service(public=false)
 */
class MessageQueueWorker {
	use ServiceContainerTrait;
	use RedisTrait;

	public function run() {
		$predis = $this->getPredis();

		while (true) {
			list ($queue_name, $message) = $predis->BRPOP(MessageQueue::REDIS_MESSAGE_QUEUE, 0);
			$message = json_decode($message, true);

			$service = $this->getServiceContainer()->get($message['service_id']);

			call_user_func_array([$service, $message['method']], $message['arguments']);
		}
	}
} 