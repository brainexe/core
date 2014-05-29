<?php

namespace Matze\Core\MessageQueue;

use Matze\Core\EventDispatcher\AbstractEventListener;
use Matze\Core\EventDispatcher\BackgroundEvent;
use Matze\Core\EventDispatcher\DelayedEvent;

/**
 * @EventListener
 */
class MessageQueueListener extends AbstractEventListener {

	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents() {
		return [
			DelayedEvent::DELAYED => 'onDelayedEvent',
			BackgroundEvent::BACKGROUND => 'onBackgroundEvent'
		];
	}

	/**
	 * @param DelayedEvent $event
	 */
	public function onDelayedEvent(DelayedEvent $event) {
		/** @var MessageQueueGateway $message_queue_gateway */
		$message_queue_gateway = $this->getService('MessageQueueGateway');

		$message_queue_gateway->addEvent($event->event, $event->timestamp);
	}

	/**
	 * @param BackgroundEvent $event
	 */
	public function onBackgroundEvent(BackgroundEvent $event) {
		/** @var MessageQueueGateway $message_queue_gateway */
		$message_queue_gateway = $this->getService('MessageQueueGateway');

		$message_queue_gateway->addEvent($event->event, 0);
	}
}