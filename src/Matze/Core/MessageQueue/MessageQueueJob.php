<?php

namespace Matze\Core\MessageQueue;

use Matze\Core\EventDispatcher\AbstractEvent;

class MessageQueueJob {

	/**
	 * @var AbstractEvent
	 */
	public $event;

	/**
	 * @var string
	 */
	public $event_id;

	/**
	 * @var integer
	 */
	public $timestamp;

	/**
	 * @param AbstractEvent $event
	 * @param string $event_id
	 * @param integer $timestamp
	 */
	function __construct(AbstractEvent $event = null, $event_id, $timestamp) {
		$this->event = $event;
		$this->event_id = $event_id;
		$this->timestamp = $timestamp;
	}

	/**
	 * @return string
	 */
	public function getShortId() {
		return explode(':', $this->event_id, 2)[1];
	}


} 