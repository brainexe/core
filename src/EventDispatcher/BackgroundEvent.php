<?php

namespace BrainExe\Core\EventDispatcher;

use BrainExe\Core\MessageQueue\AbstractMessageQueueEvent;

class BackgroundEvent extends AbstractMessageQueueEvent {

	const BACKGROUND = 'message_queue.background';

	/**
	 * @param AbstractEvent $event
	 */
	function __construct(AbstractEvent $event) {
		$this->event_name = self::BACKGROUND;
		$this->event = $event;
	}
} 