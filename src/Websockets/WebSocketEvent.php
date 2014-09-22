<?php

namespace BrainExe\Core\Websockets;

use BrainExe\Core\EventDispatcher\AbstractEvent;

class WebSocketEvent extends AbstractEvent {

	const PUSH = 'websocket.push';

	/**
	 * @var AbstractEvent
	 */
	public $payload;

	/**
	 * @param AbstractEvent $payload
	 */
	public function __construct(AbstractEvent $payload) {
		$this->event_name = self::PUSH;
		$this->payload = $payload;
	}

}