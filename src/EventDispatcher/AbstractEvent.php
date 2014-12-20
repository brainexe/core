<?php

namespace BrainExe\Core\EventDispatcher;

use Symfony\Component\EventDispatcher\Event;

abstract class AbstractEvent extends Event
{

    /**
     * @var string
     */
    public $event_name;

    /**
     * @param string $eventName
     */
    public function __construct($eventName)
    {
        $this->event_name = $eventName;
    }
}
