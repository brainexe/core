<?php

namespace BrainExe\Core\EventDispatcher;

use BrainExe\Core\Websockets\WebSocketEvent;
use RuntimeException;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher as SymfonyEventDispatcher;
use Symfony\Component\EventDispatcher\Event;

class EventDispatcher extends SymfonyEventDispatcher
{

    /**
     * @var bool
     */
    private $enabled = true;

    /**
     * @var Catchall[]
     */
    private $catchall = [];

    /**
     * @param Catchall $dispatcher
     */
    public function addCatchall(Catchall $dispatcher)
    {
        $dispatcher->setDispatcher($this);
        $this->catchall[] = $dispatcher;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @param string $eventName
     * @param Event $event
     * @return Event|void
     */
    public function dispatch($eventName, Event $event = null)
    {
        if (empty($event)) {
            throw new RuntimeException("You have to pass an Event into EventDispatcher::dispatch");
        }

        $event->setDispatcher($this); // @todo needed?

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
        $this->dispatch($event->event_name, $event);
        if ($event instanceof PushViaWebsocket) {
            /** @var AbstractEvent $event */
            $this->dispatchAsWebsocketEvent($event);
        }
    }

    /**
     * @param AbstractEvent $event
     */
    public function dispatchAsWebsocketEvent(AbstractEvent $event)
    {
        $wrappedEvent = new WebSocketEvent($event);

        $this->dispatch($wrappedEvent->event_name, $wrappedEvent);
    }

    /**
     * @param AbstractEvent $event
     * @param integer|null $timestamp
     */
    public function dispatchInBackground(AbstractEvent $event, $timestamp = 0)
    {
        if (!$this->enabled) {
            $this->dispatchEvent($event);
            return;
        }

        if ($timestamp) {
            $wrapper = new DelayedEvent($event, $timestamp);
        } else {
            $wrapper = new BackgroundEvent($event);
        }

        $this->dispatchEvent($wrapper);
    }
}
