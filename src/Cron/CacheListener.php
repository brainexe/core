<?php

namespace BrainExe\Core;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\Annotations\Listen;
use BrainExe\Core\DependencyInjection\CompilerPass\Cron;
use BrainExe\Core\EventDispatcher\CronEvent;
use BrainExe\Core\EventDispatcher\Events\TimingEvent;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\FileCacheTrait;
use BrainExe\Core\Traits\LoggerTrait;
use BrainExe\Core\MessageQueue\Gateway;
use BrainExe\Core\EventDispatcher\Events\ClearCacheEvent;

/**
 * @EventListener("Crons.CacheListener")
 */
class CacheListener
{

    use FileCacheTrait;
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
    public function handleRebuildCache()
    {
        $crons = $this->includeFile(Cron::CACHE_FILE);
        if (empty($crons)) {
            return;
        }

        foreach ($this->gateway->getEventsByType(CronEvent::CRON) as $id => $job) {
            /** @var CronEvent $event */
            $event = $job->event;
            $name = $event->getEvent()->timingId;
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
