<?php

namespace BrainExe\Core\Application\SelfUpdate;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\PushViaWebsocket;

class SelfUpdateEvent extends AbstractEvent implements PushViaWebsocket
{

    const TRIGGER = 'update.trigger';
    const PROCESS = 'update.process';
    const DONE = 'update.done';
    const ERROR = 'update.error';

    /**
     * @var string
     */
    public $payload;

    /**
     * @param string $eventName - self::*
     */
    public function __construct($eventName)
    {
        $this->event_name = $eventName;
    }
}
