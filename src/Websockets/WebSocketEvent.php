<?php

namespace BrainExe\Core\Websockets;

use BrainExe\Core\EventDispatcher\AbstractEvent;

/**
 * @api
 */
class WebSocketEvent extends AbstractEvent
{

    const PUSH = 'websocket.push';

    /**
     * @var AbstractEvent
     */
    public $payload;

    /**
     * @param AbstractEvent $payload
     */
    public function __construct(AbstractEvent $payload)
    {
        parent::__construct(self::PUSH);

        $this->payload = $payload;
    }
}
