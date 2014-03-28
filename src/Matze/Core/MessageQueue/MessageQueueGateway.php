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

	const REDIS_MESSAGE_QUEUE = 'message_queue';
	const REDIS_MESSAGE_META_DATA = 'message_queue_meta_data';
	const REDIS_MESSAGE_QUEUE_TYPES = 'message_queue_types:%s';

	use RedisTrait;
	use IdGeneratorTrait;

	/**
	 * @return MessageQueueJob|null
	 */
	public function fetchPendingEvent() {
		$now = time();

		$redis = $this->getRedis();
		$event_results = $redis->ZRANGEBYSCORE(self::REDIS_MESSAGE_QUEUE, 0, $now, 'WITHSCORES', 'LIMIT', 0, 1);

		if (empty($event_results)) {
			return null;
		}

		list($event_id, $timestamp) = $event_results[0];
		$event = unserialize($redis->HGET(self::REDIS_MESSAGE_META_DATA, $event_id));

		return new MessageQueueJob($event, $event_id, $timestamp);
	}

	/**
	 * @param integer $event_id
	 * @param null $event_type
	 */
	public function deleteEvent($event_id, $event_type = null) {
		if ($event_type) {
			$event_id = sprintf('%s:%s', $event_type, $event_id);
		} else {
			$event_type = explode(':', $event_id, 1)[0];
		}

		$redis = $this->getRedis();
		$redis->ZREM(self::REDIS_MESSAGE_QUEUE, $event_id);
		$redis->HDEL(self::REDIS_MESSAGE_META_DATA, $event_id);
		$redis->SREM($this->_getTypeKeyName($event_type), $event_id);
	}

	/**
	 * @param AbstractEvent $event
	 * @param integer $timestamp
	 * @return integer
	 */
	public function addEvent(AbstractEvent $event, $timestamp = 0) {
		$transaction = $this->getRedis()->pipeline();

		$random_id = $this->generateRandomId();

		$event_id = sprintf('%s:%s', $event->event_name, $random_id);

		$transaction->HSET(self::REDIS_MESSAGE_META_DATA, $event_id, serialize($event));
		$transaction->ZADD(self::REDIS_MESSAGE_QUEUE, $timestamp, $event_id);
		$transaction->SADD($this->_getTypeKeyName($event->event_name), $event_id);

		$transaction->exec();

		return $event_id;
	}

	/**
	 * @param string $event_type
	 * @param integer $since
	 * @return MessageQueueJob[]
	 * @todo use redis index
	 */
	public function getEventsByType($event_type = null, $since = 0) {
		$redis = $this->getRedis();

		$events = [];

		$result_raw = $redis->ZRANGEBYSCORE(self::REDIS_MESSAGE_QUEUE, $since, '+inf', array('withscores' => TRUE));

		foreach ($result_raw as $event_id => $timestamp) {
			if (empty($event_type) || strpos($event_id, "$event_type:") === 0) {
				$event_raw = $redis->HGET(self::REDIS_MESSAGE_META_DATA, $event_id);

				$events[$event_id] = new MessageQueueJob(unserialize($event_raw), $event_id, $timestamp);
			}
		}

		return $events;
	}

	/**
	 * @return integer
	 */
	public function countJobs() {
		return (int)$this->getRedis()->ZCARD(self::REDIS_MESSAGE_QUEUE);
	}

	/**
	 * @param string $type
	 * @return string
	 */
	private function _getTypeKeyName($type) {
		return sprintf(self::REDIS_MESSAGE_QUEUE_TYPES, $type);
	}
}