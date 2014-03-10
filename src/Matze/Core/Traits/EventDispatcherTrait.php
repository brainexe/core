<?php

namespace Matze\Core\Traits;

use Matze\Core\EventDispatcher\AbstractEvent;
use Matze\Core\EventDispatcher\BackgroundEvent;
use Matze\Core\EventDispatcher\DelayedEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

trait EventDispatcherTrait {

	/**
	 * @var EventDispatcher
	 */
	private $_event_dispatcher;

	/**
	 * @Inject("@EventDispatcher")
	 */
	public function setEventDispatcher(EventDispatcher $event_dispatcher) {
		$this->_event_dispatcher = $event_dispatcher;
	}

	/**
	 * @return EventDispatcher
	 */
	protected function getEventDispatcher() {
		return $this->_event_dispatcher;
	}

	/**
	 * @param AbstractEvent $event
	 */
	protected function dispatchEvent(AbstractEvent $event) {
		$this->_event_dispatcher->dispatch($event->event_name, $event);
	}

	/**
	 * @param AbstractEvent $event
	 * @param integer|null $timestamp
	 */
	protected function dispatchInBackground(AbstractEvent $event, $timestamp = 0) {
		if ($timestamp) {
			$wrapper = new DelayedEvent($event, $timestamp);
		} else {
			$wrapper = new BackgroundEvent($event);
		}

		$this->dispatchEvent($wrapper);
	}

} 
