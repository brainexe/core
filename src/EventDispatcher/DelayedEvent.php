<?php

namespace BrainExe\Core\EventDispatcher;

use BrainExe\MessageQueue\Event\MessageQueueEvent;

class DelayedEvent extends MessageQueueEvent
{

    const DELAYED = 'message_queue.delayed';

    /**
     * @var integer
     */
    public $timestamp;

    /**
     * @param AbstractEvent $event
     * @param int $timestamp
     */
    public function __construct(AbstractEvent $event, $timestamp)
    {
        $this->event_name = self::DELAYED;
        $this->event      = $event;
        $this->timestamp  = $timestamp;
    }
}
