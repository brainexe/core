<?php

namespace Matze\Core\MessageQueue;

use Matze\Core\EventDispatcher\MessageQueueEvent;
use Matze\Core\Traits\RedisTrait;

/**
 * @Service
 */
class MessageQueueGateway {

	use RedisTrait;

	/**
	 * @param integer $timeout
	 * @return MessageQueueEvent|null
	 */
	public function waitForNewJob($timeout = 0) {
		$meta_data_id = $this->getPredis()->BRPOP(MessageQueue::REDIS_MESSAGE_QUEUE, $timeout)[1];

		if (empty($meta_data_id)) {
			return null;
		}

		$payload = $this->_fetchMetaData($meta_data_id);
		if (empty($payload)) {
			return null;
		}

		return new MessageQueueEvent($payload['service_id'], $payload['method'], json_decode($payload['arguments'], true));
	}

	/**
	 * @param array $event
	 */
	public function addJob(array $event) {
		$pipeline = $this->getPredis()->transaction();

		$metadata_id = mt_rand();
		$meta_data_key = $this->_getMetadataKey($metadata_id);

		$pipeline->HMSET($meta_data_key, $event);
		$pipeline->LPUSH(MessageQueue::REDIS_MESSAGE_QUEUE, $metadata_id);

		$pipeline->execute();
	}

	/**
	 * @param integer $meta_data_id
	 * @return array|null
	 */
	private function _fetchMetaData($meta_data_id) {
		$predis = $this->getPredis()->transaction();
		$key = $this->_getMetadataKey($meta_data_id);

		$predis->HGETALL($key);
		$predis->DEL($key);

		return $predis->execute()[0];
	}

	/**
	 * @param integer $metadata_id
	 * @return string
	 */
	private function _getMetadataKey($metadata_id) {
		return sprintf(MessageQueue::REDIS_MESSAGE_META_DATA, $metadata_id);
	}
} 