<?php

namespace BrainExe\Core\Traits;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\EventDispatcher;

trait EventDispatcherTrait
{

    /**
     * @var EventDispatcher
     */
    private $_event_dispatcher;

    /**
     * @Inject("@EventDispatcher")
     * @param EventDispatcher $event_dispatcher
     */
    public function setEventDispatcher(EventDispatcher $event_dispatcher)
    {
        $this->_event_dispatcher = $event_dispatcher;
    }

    /**
     * @param AbstractEvent $event
     */
    public function dispatchEvent(AbstractEvent $event)
    {
        $this->_event_dispatcher->dispatchEvent($event);
    }

    /**
     * @param AbstractEvent $event
     * @param integer|null $timestamp
     */
    public function dispatchInBackground(AbstractEvent $event, $timestamp = 0)
    {
        $this->_event_dispatcher->dispatchInBackground($event, $timestamp);
    }
}
