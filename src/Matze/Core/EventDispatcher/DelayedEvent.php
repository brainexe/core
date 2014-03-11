<?php

namespace Matze\Core\EventDispatcher;

use Matze\Core\MessageQueue\AbstractMessageQueueEvent;

class DelayedEvent extends AbstractMessageQueueEvent {

	const DELAYED = 'message_queue.delayed';

	/**
	 * @var integer
	 */
	public $timestamp;

	/**
	 * @param AbstractEvent $event
	 * @param integer $timestamp
	 */
	function __construct(AbstractEvent $event, $timestamp) {
		$this->event_name = self::DELAYED;
		$this->event = $event;
		$this->timestamp = $timestamp;
	}
} 