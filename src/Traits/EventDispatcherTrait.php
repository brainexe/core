<?php

namespace BrainExe\Core\Traits;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\EventDispatcher;

trait EventDispatcherTrait
{

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @Inject("@EventDispatcher")
     * @param EventDispatcher $dispatcher
     */
    public function setEventDispatcher(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param AbstractEvent $event
     */
    public function dispatchEvent(AbstractEvent $event)
    {
        $this->dispatcher->dispatchEvent($event);
    }

    /**
     * @param AbstractEvent $event
     * @param integer|null $timestamp
     */
    public function dispatchInBackground(AbstractEvent $event, $timestamp = 0)
    {
        $this->dispatcher->dispatchInBackground($event, $timestamp);
    }
}
