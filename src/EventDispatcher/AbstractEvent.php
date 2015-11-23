<?php

namespace BrainExe\Core\EventDispatcher;

use Symfony\Component\EventDispatcher\Event;

/**
 * @api
 */
abstract class AbstractEvent extends Event
{

    /**
     * @var string
     */
    public $eventName;

    /**
     * @param string $eventName
     */
    public function __construct($eventName)
    {
        $this->eventName = $eventName;
    }
}
