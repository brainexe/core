<?php

namespace Matze\Core\EventDispatcher;

use Matze\Core\MessageQueue\AbstractMessageQueueEvent;

class BackgroundEvent extends AbstractMessageQueueEvent {

	const NAME = 'background';

	/**
	 * @param AbstractEvent $event
	 */
	function __construct(AbstractEvent $event) {
		$this->event_name = self::NAME;
		$this->event = $event;
	}
} 