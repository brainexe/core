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
    public function __construct(string $eventName)
    {
        $this->eventName = $eventName;
    }

    /**
     * @return string
     */
    public function getEventName() : string
    {
        return $this->eventName;
    }
}
