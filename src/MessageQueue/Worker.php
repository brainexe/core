<?php

namespace BrainExe\Core\MessageQueue;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\CronEvent;
use BrainExe\Core\EventDispatcher\JobEvent;
use BrainExe\Core\Stats\Event;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\LoggerTrait;
use Cron\CronExpression;
use Exception;

/**
 * @Service("MessageQueue.Worker", public=false)
 */
class Worker
{

    use LoggerTrait;
    use EventDispatcherTrait;

    /**
     * @var Gateway
     */
    private $gateway;

    /**
     * @Inject({"@MessageQueue.Gateway"})
     * @param Gateway $gateway
     */
    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @param Job $job
     */
    public function executeJob(Job $job)
    {
        try {
            $this->execute($job);
        } catch (Exception $e) {
            $this->error($e->getMessage(), ['exception' => $e]);
            $this->gateway->restoreJob($job);
        }
    }

    /**
     * @param Job $job
     */
    private function execute(Job $job)
    {
        $event = $job->event;
        if ($event instanceof CronEvent) {
            $event = $this->handleCronEvent($job, $event);
        }

        $logStart = microtime(true);

        $this->dispatchEvent($event);

        $neededTime = microtime(true) - $logStart;

        $this->info(
            sprintf(
                '[MQ]: %s. Time: %0.2fms',
                $event->eventName,
                $neededTime * 1000
            ),
            ['channel' => 'message_queue']
        );

        $event = new Event(
            Event::INCREASE,
            sprintf('message_queue:handled:%s', $event->eventName)
        );
        $this->dispatchEvent($event);

        $handledEvent = new JobEvent(JobEvent::HANDLED, $job);
        $this->dispatcher->dispatchEvent($handledEvent);
    }

    /**
     * @param Job $job
     * @param CronEvent $event
     * @return AbstractEvent
     */
    private function handleCronEvent(Job $job, CronEvent $event)
    {
        if (!$event->isPropagationStopped()) {
            $cron = CronExpression::factory($event->expression);
            $nextRun = $cron->getNextRunDate()->getTimestamp();

            $job->timestamp = $nextRun;
            $this->gateway->addJob($job);
        }

        return $event->event;
    }
}