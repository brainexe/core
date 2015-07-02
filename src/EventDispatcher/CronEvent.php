<?php

namespace BrainExe\Core\EventDispatcher;

use BrainExe\MessageQueue\Event\MessageQueueEvent;

/**
 * @api
 */
class CronEvent extends MessageQueueEvent
{

    const CRON = 'message_queue.cron';

    /**
     * @var string
     */
    public $expression;

    /**
     * @param AbstractEvent $event
     * @param int $expression
     */
    public function __construct(AbstractEvent $event, $expression)
    {
        parent::__construct(self::CRON);
        $this->event      = $event;
        $this->expression = $expression;
    }
}
