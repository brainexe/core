<?php

namespace BrainExe\Tests\MessageQueue;

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
     * @var Time|MockObject
     */
    private $time;

    public function setUp()
    {
        $this->messageQueue = $this->createMock(MessageQueueGateway::class);
        $this->time         = $this->createMock(Time::class);

        $this->subject = new Controller($this->messageQueue);
        $this->subject->setTime($this->time);
    }

    public function testDeleteJob()
    {
        $jobId     = 10;
        $eventType = 'eventType';

        $request = new Request();

        $this->messageQueue
            ->expects($this->once())
            ->method('deleteEvent')
            ->with($jobId, $eventType)
            ->willReturn(true);

        $actual = $this->subject->deleteJob($request, $eventType, $jobId);

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
