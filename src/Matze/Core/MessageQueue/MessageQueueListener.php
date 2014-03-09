<?php

namespace Matze\Core\MessageQueue;

use Matze\Core\EventDispatcher\AbstractEventListener;
use Matze\Core\EventDispatcher\MessageQueueEvent;
use Matze\Core\Traits\RedisTrait;
use Matze\Core\Traits\ServiceContainerTrait;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @EventListener(public = false)
 */
class MessageQueueListener extends AbstractEventListener {

	use ServiceContainerTrait;

	public static function getSubscribedEvents() {
		return [
			MessageQueueEvent::NAME => 'onMessageQueueEvent'
		];
	}

	/**
	 * @param MessageQueueEvent $event
	 */
	public function onMessageQueueEvent(MessageQueueEvent $event) {
		/** @var MessageQueueGateway $message_queue_gateway */
		$message_queue_gateway = $this->getService('MessageQueueGateway');

		$event->setDispatcher(new EventDispatcher()); // TODO
		$message_queue_gateway->addEvent($event, 0);
	}
}