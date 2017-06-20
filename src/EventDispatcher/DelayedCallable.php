<?php

namespace BrainExe\Core\EventDispatcher;

use BrainExe\Core\Annotations\Listen;
use BrainExe\Core\Annotations\Service;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;

/**
 * @Service
 */
class DelayedCallable
{

    const FINISHED_EVENT = 'request.finished';

    /**
     * @var callable[]
     */
    private $delayed = [];

    /**
     * @Listen(DelayedCallableEvent::DELAYED)
     *
     * @param DelayedCallableEvent $event
     */
    public function onDelayedCallable(DelayedCallableEvent $event): void
    {
        $this->delayed[] = $event->getCallable();
    }

    /**
     * @Listen(DelayedCallable::FINISHED_EVENT);
     *
     * @param FinishRequestEvent $event
     */
    public function onRequestEnded(FinishRequestEvent $event): void
    {
        foreach ($this->delayed as $callable) {
            $callable();
        }
        $this->delayed = [];
    }


}
