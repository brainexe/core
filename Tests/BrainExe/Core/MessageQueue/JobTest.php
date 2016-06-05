<?php

namespace Tests\BrainExe\Core\MessageQueue;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\MessageQueue\Job;
use PHPUnit_Framework_TestCase as TestCase;

class JobTest extends TestCase
{

    /**
     * @var Job
     */
    private $subject;

    public function setup()
    {
        /** @var AbstractEvent $event */
        $event     = $this->createMock(AbstractEvent::class);
        $jobId  = "type:111";
        $timestamp = 1000;

        $this->subject = new Job($event, $jobId, $timestamp);
    }

    public function testEvent()
    {
        $this->assertEquals('type:111', $this->subject->jobId);
        $this->assertEquals('type:111', $this->subject->getJobId());
        $this->assertEquals(1000, $this->subject->getTimestamp());
    }
}
