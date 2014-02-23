<?php

namespace Matze\Core\MessageQueue;

use Matze\Annotations\Annotations as DI;
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
	public function run() {
		$predis = $this->getPredis();

		while (true) {
			$message_json = $predis->BRPOP(MessageQueue::REDIS_MESSAGE_QUEUE, 0)[1];
			$message = json_decode($message_json, true);

			$service = $this->getServiceContainer()->get($message['service_id']);

			$start = microtime(true);
			call_user_func_array([$service, $message['method']], $message['arguments']);
			$time = microtime(true) - $start;

			$this->info(sprintf('[MQ]: %s->%s(%s). Time: %0.2fms',
				$message['service_id'], $message['method'], implode(', ', $message['arguments']), $time * 1000)
			);
		}
	}
} 