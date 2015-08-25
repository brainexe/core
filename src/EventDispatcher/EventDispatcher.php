<?php

namespace BrainExe\Core\EventDispatcher;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Websockets\WebSocketEvent;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @Service("EventDispatcher", public=false)
 * @api
 */
class EventDispatcher extends ContainerAwareEventDispatcher
{

    /**
     * @var bool
     */
    private $enabled = true;

    /**
     * @var EventDispatcherInterface[]
     */
    private $catchall = [];

    /**
     * @Inject({"@service_container", "%message_queue.enabled%"})
     * @param ContainerInterface $container
     * @param bool $backgroundEnabled
     */
    public function __construct(ContainerInterface $container, $backgroundEnabled)
    {
        parent::__construct($container);
        $this->enabled = $backgroundEnabled;
    }

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
