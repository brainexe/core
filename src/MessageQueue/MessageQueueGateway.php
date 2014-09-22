<?php

namespace BrainExe\Core\MessageQueue;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\Redis\RedisScripts;
use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\RedisTrait;
use BrainExe\Core\Redis\RedisScriptInterface;
use Redis;

/**
 * @Service(tags={{"name" = "redis_script"}})
 */
class MessageQueueGateway implements RedisScriptInterface {

	use RedisTrait;
	use IdGeneratorTrait;

	const REDIS_MESSAGE_QUEUE = 'message_queue';
	const REDIS_MESSAGE_META_DATA = 'message_queue_meta_data';
	const REDIS_MESSAGE_QUEUE_TYPES = 'message_queue_types:%s';

	const SCRIPT_MESSAGE_QUEUE = 'message_queue';

	/**
	 * @var RedisScripts
	 */
	private $_redis_script;

	/**
	 * @Inject("@RedisScripts")
	 */
	public function __construct(RedisScripts $redis_script) {
		$this->_redis_script = $redis_script;
	}

	/**
	 * @return MessageQueueJob|null
	 */
	public function fetchPendingEvent() {
		$now = time();

		$sha1 = $this->_redis_script->getSha1(self::SCRIPT_MESSAGE_QUEUE);
		$result = $this->getRedis()->EVALSHA($sha1, [$now], 1);

		if (empty($result)) {
			return null;
		}

		list($event_id, $timestamp, $payload) = $result;

		return new MessageQueueJob(unserialize($payload), $event_id, $timestamp);
	}

	/**
	 * @param integer $event_id
	 * @param null $event_type
	 */
	public function deleteEvent($event_id, $event_type = null) {
		if ($event_type) {
			$event_id = sprintf('%s:%s', $event_type, $event_id);
		} else {
			$event_type = explode(':', $event_id, 2)[0];
		}

		$pipeline = $this->getRedis()->multi(Redis::PIPELINE);
		$pipeline->ZREM(self::REDIS_MESSAGE_QUEUE, $event_id);
		$pipeline->HDEL(self::REDIS_MESSAGE_META_DATA, $event_id);
		$pipeline->SREM($this->_getTypeKeyName($event_type), $event_id);
		$pipeline->exec();
	}

	/**
	 * @param AbstractEvent $event
	 * @param integer $timestamp
	 * @return integer
	 */
	public function addEvent(AbstractEvent $event, $timestamp = 0) {
		$pipeline = $this->getRedis()->multi(Redis::PIPELINE);

		$random_id = $this->generateRandomId();

		$event_id = sprintf('%s:%s', $event->event_name, $random_id);

		$pipeline->HSET(self::REDIS_MESSAGE_META_DATA, $event_id, serialize($event));
		$pipeline->ZADD(self::REDIS_MESSAGE_QUEUE, $timestamp, $event_id);
		$pipeline->SADD($this->_getTypeKeyName($event->event_name), $event_id);

		$pipeline->exec();

		return $event_id;
	}

	/**
	 * @param string $event_type
	 * @param integer $since
	 * @return MessageQueueJob[]
	 * @todo use redis index or lua script?
	 */
	public function getEventsByType($event_type = null, $since = 0) {
		$redis = $this->getRedis();

		$events = [];

		$result_raw = $redis->ZRANGEBYSCORE(self::REDIS_MESSAGE_QUEUE, $since, '+inf', ['withscores' => true]);

		foreach ($result_raw as $event_id => $timestamp) {
			if (empty($event_type) || strpos($event_id, "$event_type:") === 0) {
				$event_raw = $redis->HGET(self::REDIS_MESSAGE_META_DATA, $event_id);

				$events[$event_id] = new MessageQueueJob(unserialize($event_raw), $event_id, $timestamp);
			}
		}

		return $events;
	}

	/**
	 * @param string $event_id
	 */
	public function restoreEvent($event_id) {
		$this->getRedis()->ZADD(self::REDIS_MESSAGE_QUEUE, 0, $event_id);
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

	/**
	 * @return string[]
	 */
	public static function getRedisScripts() {
		return [
			self::SCRIPT_MESSAGE_QUEUE =>'
				local result = redis.call("ZRANGEBYSCORE", "message_queue", 0, KEYS[1], "withscores", "LIMIT", 0, 1)
				if result == nil then
					return nil
				else
					local event_id = result[1]
					local timestamp = result[2]
					local result = redis.call("HGET", "message_queue_meta_data", event_id)

					redis.call("ZREM", "message_queue", event_id)

					return {event_id, timestamp, result}
				end
			'
		];
	}
}