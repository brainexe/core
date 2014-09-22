<?php

namespace BrainExe\Core\MessageQueue;

use BrainExe\Core\EventDispatcher\AbstractEvent;

abstract class AbstractMessageQueueEvent extends AbstractEvent {

	/**
	 * @var AbstractEvent
	 */
	public $event;
} 