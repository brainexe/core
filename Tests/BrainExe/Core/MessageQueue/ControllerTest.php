<?php

namespace BrainExe\Tests\MessageQueue;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\MessageQueue\Controller;
use BrainExe\Core\MessageQueue\Gateway as MessageQueueGateway;
use BrainExe\Core\Util\Time;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers BrainExe\Core\MessageQueue\Controller
 */
class ControllerTest extends TestCase
{

    /**
     * @var Controller
     */
    private $subject;

    /**
     * @var MessageQueueGateway|MockObject
     */
    private $messageQueue;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    /**
     * @var Time|MockObject
     */
    private $time;

    public function setUp()
    {
        $this->messageQueue = $this->getMock(MessageQueueGateway::class, [], [], '', false);
        $this->dispatcher   = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->time         = $this->getMock(Time::class);

        $this->subject = new Controller($this->messageQueue);
        $this->subject->setEventDispatcher($this->dispatcher);
        $this->subject->setTime($this->time);
    }

    public function testDeleteJob()
    {
        $jobId = 10;
        $request = new Request();

        $this->messageQueue
            ->expects($this->once())
            ->method('deleteEvent')
            ->willReturn($jobId);

        $actual = $this->subject->deleteJob($request, $jobId);

        $this->assertTrue($actual);
    }

    public function testGetJobs()
    {
        $type = 'dummyevent';
        $now  = 1000;
        $jobs = ['jobs'];

        $request = new Request();
        $request->query->set('futureOnly', 1);

        $this->time
            ->expects($this->once())
            ->method('now')
            ->willReturn($now);

        $this->messageQueue
            ->expects($this->once())
            ->method('getEventsByType')
            ->with($type, $now)
            ->willReturn($jobs);

        $actual = $this->subject->getJobs($request, $type);

        $this->assertEquals($jobs, $actual);
    }
}
