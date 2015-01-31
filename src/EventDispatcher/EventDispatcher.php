<?php

namespace BrainExe\Core\EventDispatcher;

use BrainExe\Core\Websockets\WebSocketEvent;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher as SymfonyEventDispatcher;

class EventDispatcher extends SymfonyEventDispatcher
{

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @inject("%message_queue.enabled%")
     * @param bool $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @param AbstractEvent $event
     */
    public function dispatchEvent(AbstractEvent $event)
    {
        $this->dispatch($event->event_name, $event);
        if ($event instanceof PushViaWebsocketInterface) {
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
