<?php

namespace Tests\BrainExe\Core\EventDispatcher;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\CronEvent;
use BrainExe\Core\MessageQueue\Job;
use PHPUnit_Framework_TestCase as TestCase;

class CronEventTest extends TestCase
{
    public function testConstructor()
    {
        /** @var AbstractEvent $wrapped */
        $wrapped = $this->createMock(AbstractEvent::class);
        $cron    = 3600;
        $job     = $this->createMock(Job::class);

        $event = new CronEvent($wrapped, $cron);
        $event->setJob($job);

        $this->assertEquals($wrapped, $event->event);
        $this->assertEquals($wrapped, $event->getEvent());
        $this->assertEquals($cron, $event->getExpression());
        $this->assertEquals($job, $event->getJob());
    }
}
