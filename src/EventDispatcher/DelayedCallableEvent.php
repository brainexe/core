<?php

namespace BrainExe\Core\EventDispatcher;

use BrainExe\Core\MessageQueue\Event\MessageQueueEvent;

/**
 * @api
 */
class DelayedCallableEvent extends MessageQueueEvent
{

    const DELAYED = 'delayed.callable';

    /**
     * @var callable
     */
    private $callable;

    /**
     * @param callable $callable
     */
    public function __construct(callable $callable)
    {
        parent::__construct(self::DELAYED);

        $this->callable = $callable;
    }

    /**
     * @return callable
     */
    public function getCallable(): callable
    {
        return $this->callable;
    }
}
