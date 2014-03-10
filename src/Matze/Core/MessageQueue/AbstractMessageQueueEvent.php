<?php

namespace Matze\Core\MessageQueue;

use Matze\Core\EventDispatcher\AbstractEvent;

abstract class AbstractMessageQueueEvent extends AbstractEvent {

	/**
	 * @var AbstractEvent
	 */
	public $event;
} 