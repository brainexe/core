<?php

namespace BrainExe\Core\EventDispatcher;

use BrainExe\MessageQueue\Event\AbstractMessageQueueEvent;

class DelayedEvent extends AbstractMessageQueueEvent
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
