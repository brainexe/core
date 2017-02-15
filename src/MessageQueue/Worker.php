<?php

namespace BrainExe\Core\MessageQueue;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;

use BrainExe\Core\Cron\Expression;
use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\CronEvent;
use BrainExe\Core\EventDispatcher\JobEvent;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\LoggerTrait;
use BrainExe\Core\Traits\TimeTrait;

use Throwable;

/**
 * @Service("MessageQueue.Worker", public=false)
 */
class Worker
{

    use LoggerTrait;
    use TimeTrait;
    use EventDispatcherTrait;

    /**
     * @var Gateway
     */
    private $gateway;

    /**
     * @var Expression
     */
    private $cron;

    /**
     * @Inject({
     *     "@MessageQueue.Gateway",
     *     "@Core.Cron.Expression"
     * })
     * @param Gateway $gateway
     * @param Expression $cron
     */
    public function __construct(Gateway $gateway, Expression $cron)
    {
        $this->gateway = $gateway;
        $this->cron    = $cron;
    }

    /**
     * @param Job $job
     */
    public function executeJob(Job $job)
    {
        try {
            $this->execute($job);
        } catch (Throwable $e) {
            $this->error($e->getMessage(), ['exception' => $e]);
            $this->gateway->restoreJob($job);
        }
    }

    /**
     * @param Job $job
     */
    private function execute(Job $job)
    {
        $event = $job->getEvent();
        if ($event instanceof CronEvent) {
            $event = $this->handleCronEvent($job, $event);
        }

        $logStart = microtime(true);

        $this->dispatchEvent($event);

        $neededTime = microtime(true) - $logStart;

        $this->info(
            sprintf(
                '[MQ]: %s. Time: %0.2fms',
                $event->getEventName(),
                $neededTime * 1000
            ),
            [
                'channel'   => 'message_queue',
                'time'      => round($neededTime * 1000, 2),
                'eventName' => $event->getEventName(),
                'jobId'     => $job->getJobId(),
                'event'     => json_encode($event)
            ]
        );

        $handledEvent = new JobEvent(JobEvent::HANDLED, $job);
        $this->dispatcher->dispatchEvent($handledEvent);
    }

    /**
     * @param Job $job
     * @param CronEvent $event
     * @return AbstractEvent
     */
    private function handleCronEvent(Job $job, CronEvent $event) : AbstractEvent
    {
        if (!$event->isPropagationStopped()) {
            $nextRun = $this->cron->getNextRun($event->getExpression());

            $job->setStartTime($this->now());
            $job->setTimestamp($nextRun);
            $this->gateway->addJob($job);
        }

        return $event->getEvent();
    }
}
