<?php

namespace BrainExe\Core\EventDispatcher;

use BrainExe\MessageQueue\Event\MessageQueueEvent;

/**
 * @api
 */
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
        parent::__construct(self::DELAYED);
        $this->event      = $event;
        $this->timestamp  = $timestamp;
    }
}
