<?php

namespace BrainExe\Tests\Stats;

use BrainExe\Core\Stats\Controller;
use BrainExe\Core\Stats\Stats;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use BrainExe\MessageQueue\Gateway as MessageQueueGateway;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers BrainExe\Core\Stats\Controller
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
     * @var Stats|MockObject
     */
    private $stats;

    public function setUp()
    {
        $this->messageQueue = $this->getMock(MessageQueueGateway::class, [], [], '', false);
        $this->dispatcher   = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->stats        = $this->getMock(Stats::class, [], [], '', false);

        $this->subject = new Controller($this->stats, $this->messageQueue);
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testIndex()
    {
        $eventsByType     = ['events'];
        $messageQueueJobs = 10;

        $this->messageQueue
            ->expects($this->once())
            ->method('getEventsByType')
            ->willReturn($eventsByType);

        $this->messageQueue
            ->expects($this->once())
            ->method('countJobs')
            ->willReturn($messageQueueJobs);
        $this->stats
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([
                'foo'  => 'bar',
                'foo1' => 'bar1'
            ]);

        $actualResult = $this->subject->index();

        $expectedResult = [
            'jobs' => $eventsByType,
            'stats' => [
                'foo'  => 'bar',
                'foo1' => 'bar1',
                'message_queue:queued' => $messageQueueJobs
            ],
        ];

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testDeleteJob()
    {
        $jobId = 10;
        $request = new Request();
        $request->request->set('job_id', $jobId);

        $this->messageQueue
            ->expects($this->once())
            ->method('deleteEvent')
            ->willReturn($jobId);


        $actualResult = $this->subject->deleteJob($request);

        $this->assertTrue($actualResult);
    }

    public function testResetStats()
    {
        $key     = 'mockKey';
        $request = new Request();
        $request->request->set('key', $key);

        $this->stats
            ->expects($this->once())
            ->method('set')
            ->with($key, 0);

        $actualResult = $this->subject->resetStats($request);

        $this->assertTrue($actualResult);
    }
}
