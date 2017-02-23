<?php

namespace BrainExe\Core\EventDispatcher;

use BrainExe\Core\MessageQueue\Event\MessageQueueEvent;

/**
 * @api
 */
class BackgroundEvent extends MessageQueueEvent
{

    const BACKGROUND = 'message_queue.background';

    /**
     * @param AbstractEvent $event
     */
    public function __construct(AbstractEvent $event)
    {
        parent::__construct(self::BACKGROUND);

        $this->event = $event;
    }
}
