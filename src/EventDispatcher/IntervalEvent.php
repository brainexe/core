<?php

namespace BrainExe\Core\EventDispatcher;

use BrainExe\MessageQueue\Event\MessageQueueEvent;

/**
 * @api
 */
class IntervalEvent extends MessageQueueEvent
{

    const INTERVAL = 'message_queue.interval';

    /**
     * @var int
     */
    public $timestamp;

    /**
     * @var int
     */
    public $interval;

    /**
     * @param AbstractEvent $event
     * @param int $timestamp
     * @param int $interval
     */
    public function __construct(AbstractEvent $event, $timestamp, $interval)
    {
        $this->event_name = self::INTERVAL;
        $this->event      = $event;
        $this->timestamp  = $timestamp;
        $this->interval   = $interval;
    }
}
