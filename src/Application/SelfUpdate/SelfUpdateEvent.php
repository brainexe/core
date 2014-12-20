<?php

namespace BrainExe\Core\Application\SelfUpdate;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\PushViaWebsocketInterface;

class SelfUpdateEvent extends AbstractEvent implements PushViaWebsocketInterface
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
     * @param string $event_name - self::*
     */
    public function __construct($event_name)
    {
        $this->event_name = $event_name;
    }
}
