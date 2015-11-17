<?php

namespace BrainExe\Core;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\Annotations\Listen;
use BrainExe\Core\EventDispatcher\CronEvent;
use BrainExe\Core\EventDispatcher\Events\ClearCacheEvent;
use BrainExe\Core\EventDispatcher\Events\TimingEvent;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\LoggerTrait;
use BrainExe\MessageQueue\Gateway;

/**
 * @EventListener("Crons.CacheListener")
 */
class CacheListener
{

    use EventDispatcherTrait;
    use LoggerTrait;

    /**
     * @var Gateway
     */
    private $gateway;

    /**
     * @Inject("@MessageQueue.Gateway")
     * @param Gateway $gateway
     */
    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @Listen(ClearCacheEvent::NAME)
     */
    public function handleRebuildCache(ClearCacheEvent $job)
    {
        $crons = require ROOT . 'cache/crons.php';

        foreach ($this->gateway->getEventsByType(CronEvent::CRON) as $id => $job) {
            $name = $job->event->event->timingId;
            if (isset($crons[$name])) {
                unset($crons[$name]);
            }
        }

        foreach ($crons as $timingId => $expression) {
            $event = new CronEvent(
                new TimingEvent($timingId),
                $expression
            );

            $this->dispatchEvent($event);

            $this->debug(sprintf('Registered cron "%s" with expression: "%s"', $timingId, $expression));
        }
    }
}
