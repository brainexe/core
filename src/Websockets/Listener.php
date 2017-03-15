<?php

namespace BrainExe\Core\Websockets;

use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\Redis\Predis;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener
 */
class Listener implements EventSubscriberInterface
{

    const CHANNEL = 'websocket:push';

    /**
     * @var Predis
     */
    private $redis;

    /**
     * @param Predis $client
     */
    public function __construct(Predis $client)
    {
        $this->redis = $client;
    }

    /**
     * {@inheritdoc}
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
        $this->redis->publish(self::CHANNEL, json_encode($event->getPayload()));
    }
}
