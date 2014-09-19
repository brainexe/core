<?php

namespace Matze\Core\Websockets;

use Matze\Core\EventDispatcher\AbstractEventListener;
use Matze\Core\Traits\EventDispatcherTrait;
use Matze\Core\Traits\RedisTrait;

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
