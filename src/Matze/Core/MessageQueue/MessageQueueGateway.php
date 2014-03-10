<?php

namespace Matze\Core\MessageQueue;

use Matze\Core\EventDispatcher\AbstractEvent;
use Matze\Core\Traits\IdGeneratorTrait;
use Matze\Core\Traits\RedisTrait;
use Symfony\Component\EventDispatcher\Event;

/**
 * @Service
 */
class MessageQueueGateway {

	use RedisTrait;
	use IdGeneratorTrait;

	/**
	 * @return Event[]
	 */
	public function fetchPendingEvents() {
		$options = [
			'cas' => true,
			'watch' => MessageQueue::REDIS_MESSAGE_QUEUE,
		];

		$this->getPredis()->transaction($options, function($transaction) use (&$result) {
			$result = [];

			$now = time();
			$event_results = $transaction->ZRANGEBYSCORE(MessageQueue::REDIS_MESSAGE_QUEUE, 0, $now, 'WITHSCORES');

			if (empty($event_results)) {
				return;
			}

			$event_ids = [];
			foreach ($event_results as $event_result) {
				list($event_id) = $event_result;
				$event_ids[] = $event_id;

				$result[] = unserialize($transaction->HGET(MessageQueue::REDIS_MESSAGE_META_DATA, $event_id));
			}

			$transaction->ZREM(MessageQueue::REDIS_MESSAGE_QUEUE, $event_ids);
			$transaction->HDEL(MessageQueue::REDIS_MESSAGE_META_DATA, $event_ids);
		});

		return $result;
	}

	/**
	 * @param integer $event_id
	 */
	public function deleteEvent($event_id) {
		$predis = $this->getPredis();

		$predis->ZREM(MessageQueue::REDIS_MESSAGE_QUEUE, $event_id);
		$predis->HDEL(MessageQueue::REDIS_MESSAGE_META_DATA, $event_id);
	}

	/**
	 * @param AbstractEvent $event
	 * @param integer $timestamp
	 * @return integer
	 */
	public function addEvent(AbstractEvent $event, $timestamp = 0) {
		$transaction = $this->getPredis()->transaction();

		$event_id = $this->generateRandomNumericId();

		$transaction->HSET(MessageQueue::REDIS_MESSAGE_META_DATA, $event_id, serialize($event));
		$transaction->ZADD(MessageQueue::REDIS_MESSAGE_QUEUE, $timestamp, $event_id);

		$transaction->execute();

		return $event_id;
	}
}