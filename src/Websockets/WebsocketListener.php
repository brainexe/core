<?php

namespace BrainExe\Core\Websockets;

use BrainExe\Core\EventDispatcher\AbstractEventListener;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\RedisTrait;

/**
 * @EventListener
 */
class WebsocketListener extends AbstractEventListener {

	const CHANNEL = 'websocket:push';

	use RedisTrait;
	use EventDispatcherTrait;

	/**
	 * @{@inheritdoc}
	 */
	public static function getSubscribedEvents() {
		return [
			WebSocketEvent::PUSH => 'handlePushEvent',
		];
	}

	/**
	 * @param WebSocketEvent $event
	 */
	public function handlePushEvent(WebSocketEvent $event) {
		$redis = $this->getRedis();
		print_r(json_encode($event->payload));
		$redis->publish(self::CHANNEL, json_encode($event->payload));
	}

}
