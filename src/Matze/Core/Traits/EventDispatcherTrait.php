<?php

namespace Matze\Core\Traits;

use Matze\Annotations\Annotations as DI;
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

	protected function getEventDispatcher() {
		return $this->_event_dispatcher;
	}

} 