<?php

namespace BrainExe\Core\EventDispatcher;

use BrainExe\Core\MessageQueue\Event\MessageQueueEvent;
use BrainExe\Core\Traits\JsonSerializableTrait;

/**
 * @api
 */
class CronEvent extends MessageQueueEvent implements PushViaWebsocket
{
    use JsonSerializableTrait;

    const CRON = 'message_queue.cron';

    /**
     * @var string
     */
    private $expression;

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
