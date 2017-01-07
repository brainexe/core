<?php

namespace BrainExe\Core\Cron;

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
        $crons = $this->getCrons();

        foreach ($crons as $timingId => $expression) {
            $event = new CronEvent(
                new TimingEvent($timingId),
                $expression
            );

            $this->dispatchEvent($event);
            $this->debug(sprintf('Registered cron "%s" with expression: "%s"', $timingId, $expression));
        }
    }

    /**
     * @return array
     */
    protected function getCrons(): array
    {
        $crons = $this->includeFile(Cron::CACHE_FILE);
        if (empty($crons)) {
            return [];
        }

        foreach ($this->gateway->getEventsByType(CronEvent::CRON) as $id => $job) {
            /** @var CronEvent $event */
            $event = $job->getEvent();
            /** @var TimingEvent $timingEvent */
            $timingEvent = $event->getEvent();
            $name  = $timingEvent->getTimingId();
            if (isset($crons[$name])) {
                unset($crons[$name]);
            }
        }

        return $crons;
    }
}
