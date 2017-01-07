<?php

namespace BrainExe\Core\Traits;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\MessageQueue\Job;

/**
 * @api
 */
trait EventDispatcherTrait
{

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @param EventDispatcher $dispatcher
     */
    public function setEventDispatcher(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return EventDispatcher
     */
    public function getDispatcher() : EventDispatcher
    {
        return $this->dispatcher;
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
     * @param int|null $timestamp
     * @return Job
     */
    public function dispatchInBackground(AbstractEvent $event, int $timestamp = 0)
    {
        return $this->dispatcher->dispatchInBackground($event, $timestamp);
    }
}
