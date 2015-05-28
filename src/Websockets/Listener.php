<?php

namespace BrainExe\Core\Websockets;

use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\Traits\RedisTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener("Websocket.Listener")
 */
class Listener implements EventSubscriberInterface
{

    const CHANNEL = 'websocket:push';

    use RedisTrait;

    /**
     * @{inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            WebSocketEvent::PUSH => 'handlePushEvent',
        ];
    }

    /**
     * @param WebSocketEvent $event
     */
    public function handlePushEvent(WebSocketEvent $event)
    {
        $redis = $this->getRedis();
        $redis->publish(self::CHANNEL, json_encode($event->payload));
    }
}
