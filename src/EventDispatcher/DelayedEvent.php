<?php

namespace BrainExe\Core\EventDispatcher;

use BrainExe\Core\MessageQueue\Event\MessageQueueEvent;

/**
 * @api
 */
class DelayedEvent extends MessageQueueEvent
{

    const DELAYED = 'message_queue.delayed';

    /**
     * @var int
     */
    private $timestamp;

    /**
     * @param AbstractEvent $event
     * @param int $timestamp
     */
    public function __construct(AbstractEvent $event, int $timestamp)
    {
        parent::__construct(self::DELAYED);

        $this->event      = $event;
        $this->timestamp  = $timestamp;
    }

    /**
     * @return int
     */
    public function getTimestamp() : int
    {
        return $this->timestamp;
    }
}
