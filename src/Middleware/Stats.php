<?php

namespace BrainExe\Core\Middleware;

use BrainExe\Core\Annotations\Middleware;
use BrainExe\Core\Stats\Event;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Middleware(priority=20)
 */
class Stats extends AbstractMiddleware
{

    use EventDispatcherTrait;

    /**
     * {@inheritdoc}
     */
    public function processResponse(Request $request, Response $response)
    {
        $event = new Event(Event::INCREASE, 'request:handle');
        $this->dispatchEvent($event);
    }

    /**
     * {@inheritdoc}
     */
    public function processException(Request $request, Exception $exception)
    {
        $event = new Event(Event::INCREASE, 'request:error');
        $this->dispatchEvent($event);
    }
}
