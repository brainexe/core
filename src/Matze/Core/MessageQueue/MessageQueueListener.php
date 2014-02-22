<?php

namespace Matze\Core\MessageQueue;

use Matze\Core\EventDispatcher\AbstractEventListener;
use Matze\Core\EventDispatcher\MessageQueueEvent;
use Matze\Core\Traits\RedisTrait;

/**
 * @EventListener(public = false)
 */
class MessageQueueListener extends AbstractEventListener {

	use RedisTrait;

	public static function getSubscribedEvents() {
		return [
			MessageQueueEvent::NAME => 'onMessageQueueEvent'
		];
	}

	/**
	 * @param MessageQueueEvent $event
	 */
	public function onMessageQueueEvent(MessageQueueEvent $event) {
		$this->getPredis()->LPUSH(MessageQueue::REDIS_MESSAGE_QUEUE, json_encode($event));
	}
}