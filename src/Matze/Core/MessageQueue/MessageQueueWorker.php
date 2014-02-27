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
	use RedisTrait;
	use LoggerTrait;

	/**
	 * {@inheritdoc}
	 * @todo run other workers
	 */
	public function run($timeout = 0) {
		$predis = $this->getPredis();

		while (true) {
			$message_json = $predis->BRPOP(MessageQueue::REDIS_MESSAGE_QUEUE, $timeout)[1];
			if (empty($message_json)) {
				break;
			}

			$message = json_decode($message_json, true);

			$service = $this->getService($message['service_id']);

			$start = microtime(true);
			call_user_func_array([$service, $message['method']], $message['arguments']);
			$time = microtime(true) - $start;

			$this->info(sprintf('[MQ]: %s->%s(%s). Time: %0.2fms',
				$message['service_id'], $message['method'], implode(', ', $message['arguments']), $time * 1000)
			);
		}
	}
} 
