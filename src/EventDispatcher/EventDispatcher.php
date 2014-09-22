<?php

namespace BrainExe\Core\EventDispatcher;

use BrainExe\Core\Websockets\WebSocketEvent;
use Symfony\Component\EventDispatcher\EventDispatcher as SymfonyEventDispatcher;

class EventDispatcher extends SymfonyEventDispatcher {

	/**
	 * @param AbstractEvent $event
	 */
	public function dispatchEvent(AbstractEvent $event) {
		$this->dispatch($event->event_name, $event);

		if ($event instanceof PushViaWebsocketInterface) {
			$this->dispatchAsWebsocketEvent($event);
		}
	}

	/**
	 * @param AbstractEvent $event
	 */
	public function dispatchAsWebsocketEvent(AbstractEvent $event) {
		$wrapped_event = new WebSocketEvent($event);

		$this->dispatch($wrapped_event->event_name, $wrapped_event);
	}

	/**
	 * @param AbstractEvent $event
	 * @param integer|null $timestamp
	 */
	public function dispatchInBackground(AbstractEvent $event, $timestamp = 0) {
		if ($timestamp) {
			$wrapper = new DelayedEvent($event, $timestamp);
		} else {
			$wrapper = new BackgroundEvent($event);
		}

		$this->dispatchEvent($wrapper);
	}
} 