<?php

namespace BrainExe\Core\EventDispatcher;

use Symfony\Component\EventDispatcher\Event;

abstract class AbstractEvent extends Event {

	/**
	 * @var string
	 */
	public $event_name;

	/**
	 * @param string $event_name
	 */
	function __construct($event_name) {
		$this->event_name = $event_name;
	}
} 