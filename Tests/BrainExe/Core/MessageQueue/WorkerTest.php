<?php

namespace Tests\BrainExe\Core\MessageQueue;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\CronEvent;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\MessageQueue\Gateway;
use BrainExe\Core\MessageQueue\Job;
use BrainExe\Core\MessageQueue\Worker;
use Exception;
use Monolog\Logger;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

class WorkerTest extends TestCase
{

    /**
     * @var Worker
     */
    private $subject;

    /**
     * @var Gateway|MockObject
     */
    private $gateway;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    /**
     * @var Logger|MockObject
     */
    private $logger;

    public function setup()
    {
        $this->gateway    = $this->getMock(Gateway::class, [], [], '', false);
        $this->dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->logger     = $this->getMock(Logger::class, [], [], '', false);

        $this->subject = new Worker($this->gateway);
        $this->subject->setLogger($this->logger);
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testExecute()
    {
        $jobId = 'job:0815';
        $timestamp = 0;

        /** @var AbstractEvent $event */
        $event = $this->getMock(AbstractEvent::class, [], [], '', false);
        $job = new Job($event, $jobId, $timestamp);

        $this->dispatcher
            ->expects($this->at(0))
            ->method('dispatchEvent')
            ->with($event);

        $this->subject->executeJob($job);
    }

    public function testExecuteCron()
    {
        $jobId = 'job:0815';
        $timestamp = 0;

        /** @var AbstractEvent $event */
        $event = $this->getMock(AbstractEvent::class, [], [], '', false);
        $cronEvent = new CronEvent($event, '@daily');

        $job = new Job($cronEvent, $jobId, $timestamp);

        $this->dispatcher
            ->expects($this->at(0))
            ->method('dispatchEvent')
            ->with($event);

        $this->gateway
            ->expects($this->once())
            ->method('addJob')
            ->with($job);

        $this->subject->executeJob($job);
    }

    public function testExecuteWitFail()
    {
        $jobId = 'job:0815';
        $timestamp = 0;

        /** @var AbstractEvent $event */
        $event = $this->getMock(AbstractEvent::class, [], [], '', false);
        $job = new Job($event, $jobId, $timestamp);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->willThrowException(new Exception('my error'));

        $this->logger
            ->expects($this->once())
            ->method('log')
            ->with('error', 'my error');

        $this->gateway
            ->expects($this->once())
            ->method('restoreJob')
            ->with($job);

        $this->subject->executeJob($job);
    }
}
