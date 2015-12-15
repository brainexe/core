<?php

namespace BrainExe\Core\Middleware;

use BrainExe\Core\Annotations\Middleware;
use BrainExe\Core\Stats\Event;
use BrainExe\Core\Stats\MultiEvent;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Middleware("Middleware.Stats")
 */
class Stats extends AbstractMiddleware
{

    use EventDispatcherTrait;

    /**
     * {@inheritdoc}
     */
    public function processResponse(Request $request, Response $response)
    {
        $event = new MultiEvent(MultiEvent::INCREASE, [
            sprintf('request:route:%s', $request->attributes->get('_route')) => 1,
            sprintf('request:user:%d', $request->attributes->get('user_id')) => 1,
            sprintf('response:code:%d', $response->getStatusCode())          => 1,
        ]);
        $this->dispatchEvent($event);
    }

    /**
     * {@inheritdoc}
     */
    public function processException(Request $request, Exception $exception)
    {
        $event = new MultiEvent(MultiEvent::INCREASE, [
            sprintf('exception:%s', get_class($exception)) => 1,
            sprintf('response:code:%d', 500)               => 1,
        ]);
        $this->dispatchEvent($event);
    }
}
