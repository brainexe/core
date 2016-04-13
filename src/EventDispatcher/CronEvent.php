<?php

namespace BrainExe\Core\EventDispatcher;

use BrainExe\Core\MessageQueue\Event\MessageQueueEvent;

/**
 * @api
 */
class CronEvent extends MessageQueueEvent implements PushViaWebsocket
{

    const CRON = 'message_queue.cron';

    /**
     * @var string
     */
    public $expression;

    /**
     * @param AbstractEvent $event
     * @param string $expression
     */
    public function __construct(AbstractEvent $event, string $expression)
    {
        parent::__construct(self::CRON);
        $this->event      = $event;
        $this->expression = $expression;
    }

    /**
     * @return string
     */
    public function getExpression() : string
    {
        return $this->expression;
    }
}
