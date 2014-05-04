<?php

namespace Matze\Core\Application\SelfUpdate;

use Matze\Core\EventDispatcher\AbstractEvent;

class SelfUpdateEvent extends AbstractEvent {

    const TRIGGER = 'update.trigger';
    const PROCESS = 'update.process';
    const DONE = 'update.done';
    const ERROR = 'update.error';

	/**
	 * @var string
	 */
	public $payload;

	/**
     * @param string $event_name - self::*
     */
    public function __construct($event_name) {
        $this->event_name = $event_name;
    }
} 