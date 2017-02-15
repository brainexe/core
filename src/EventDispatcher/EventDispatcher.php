<?php

namespace BrainExe\Core\EventDispatcher;


use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\MessageQueue\Job;
use BrainExe\Core\Websockets\WebSocketEvent;
use RuntimeException;

use Symfony\Component\EventDispatcher\EventDispatcher as SymfonyEventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @Service("EventDispatcher", public=false)
 * @api
 */
class EventDispatcher extends SymfonyEventDispatcher
{

    /**
     * @var EventDispatcherInterface[]
     */
    private $catchall = [];

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function addCatchall(EventDispatcherInterface $dispatcher)
    {
        $this->catchall[] = $dispatcher;
    }

    /**
     * @param string $eventName
     * @param Event $event
     * @return Event
     */
    public function dispatch($eventName, Event $event = null)
    {
        if (empty($event)) {
            throw new RuntimeException('You have to pass an Event into EventDispatcher::dispatch');
        }

        foreach ($this->catchall as $dispatcher) {
            $dispatcher->dispatch($eventName, $event);
        }

        return parent::dispatch($eventName, $event);
    }

    /**
     * @param AbstractEvent $event
     */
    public function dispatchEvent(AbstractEvent $event)
    {
        $this->dispatch($event->getEventName(), $event);

        if ($event instanceof PushViaWebsocket) {
            /** @var AbstractEvent $event */
            $this->dispatchAsWebsocketEvent($event);
        }
    }

    /**
     * @param AbstractEvent $event
     * @param int|null $timestamp
     * @return Job
     */
    public function dispatchInBackground(AbstractEvent $event, int $timestamp = 0)
    {
        if ($timestamp) {
            $wrapper = new DelayedEvent($event, $timestamp);
        } else {
            $wrapper = new BackgroundEvent($event);
        }

        $this->dispatchEvent($wrapper);

        return $wrapper->getJob();
    }

    /**
     * @param AbstractEvent $event
     */
    private function dispatchAsWebsocketEvent(AbstractEvent $event)
    {
        $wrappedEvent = new WebSocketEvent($event);

        $this->dispatch($wrappedEvent->getEventName(), $wrappedEvent);
    }
}
