<?php

namespace BrainExe\Core\EventDispatcher;

use BrainExe\MessageQueue\Event\MessageQueueEvent;

class BackgroundEvent extends MessageQueueEvent
{

    const BACKGROUND = 'message_queue.background';

    /**
     * @param AbstractEvent $event
     */
    public function __construct(AbstractEvent $event)
    {
        $this->event_name = self::BACKGROUND;
        $this->event = $event;
    }
}
