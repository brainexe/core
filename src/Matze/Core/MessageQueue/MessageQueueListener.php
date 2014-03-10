<?php

namespace Matze\Core\MessageQueue;

use Matze\Core\EventDispatcher\AbstractEventListener;
use Matze\Core\EventDispatcher\BackgroundEvent;
use Matze\Core\EventDispatcher\DelayedEvent;
use Matze\Core\Traits\RedisTrait;
use Matze\Core\Traits\ServiceContainerTrait;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @EventListener
 */
class MessageQueueListener extends AbstractEventListener {

	use ServiceContainerTrait;

	public static function getSubscribedEvents() {
		return [
			DelayedEvent::NAME => 'onDelayedEvent',
			BackgroundEvent::NAME => 'onBackgroundEvent'
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