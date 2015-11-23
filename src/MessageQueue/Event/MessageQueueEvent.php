<?php

namespace BrainExe\Core\MessageQueue\Event;

use BrainExe\Core\EventDispatcher\AbstractEvent;

/**
 * @api
 */
abstract class MessageQueueEvent extends AbstractEvent
{

    /**
     * @var AbstractEvent
     */
    public $event;
}
