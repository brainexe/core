<?php

namespace Tests\BrainExe\Core\EventDispatcher;

use BrainExe\Core\EventDispatcher\JobEvent;
use BrainExe\Core\MessageQueue\Job;
use PHPUnit_Framework_TestCase as TestCase;

class JobEventTest extends TestCase
{
    public function testConstructor()
    {
        /** @var Job $wrapped */
        $wrapped = $this->getMockWithoutInvokingTheOriginalConstructor(Job::class);

        $event = new JobEvent(JobEvent::ADDED, $wrapped);

        $this->assertEquals($wrapped, $event->getJob());
        $this->assertEquals(JobEvent::ADDED, $event->getEventName());
    }
}
