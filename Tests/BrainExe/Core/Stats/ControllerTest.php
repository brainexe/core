<?php

namespace BrainExe\Tests\Stats;

use BrainExe\Core\MessageQueue\Gateway as MessageQueueGateway;
use BrainExe\Core\Stats\Controller;
use BrainExe\Core\Stats\Stats;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Predis\Client;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers BrainExe\Core\Stats\Controller
 */
class ControllerTest extends TestCase
{

    use RedisMockTrait;

    /**
     * @var Controller
     */
    private $subject;

    /**
     * @var MessageQueueGateway|MockObject
     */
    private $messageQueue;

    /**
     * @var Stats|MockObject
     */
    private $stats;

    /**
     * @var Client|MockObject
     */
    private $redis;

    public function setUp()
    {
        $this->messageQueue = $this->getMock(MessageQueueGateway::class, [], [], '', false);
        $this->stats        = $this->getMock(Stats::class, [], [], '', false);
        $this->redis        = $this->getRedisMock();

        $this->subject = new Controller($this->stats, $this->messageQueue);
        $this->subject->setRedis($this->redis);
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
            ->method('countAllJobs')
            ->willReturn($messageQueueJobs);
        $this->stats
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([
                'foo'  => 'bar',
                'foo1' => 'bar1'
            ]);

        $this->redis
            ->expects($this->once())
            ->method('info')
            ->willReturn(['info']);

        $actualResult = $this->subject->index();

        $expectedResult = [
            'jobs' => $eventsByType,
            'stats' => [
                'foo'  => 'bar',
                'foo1' => 'bar1',
                'message_queue:queued' => $messageQueueJobs
            ],
            'redis' => ['info']
        ];

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testResetStats()
    {
        $key     = 'mockKey';
        $request = new Request();
        $request->request->set('key', $key);

        $this->stats
            ->expects($this->once())
            ->method('set')
            ->with([$key => 0]);

        $actualResult = $this->subject->resetStats($request);

        $this->assertTrue($actualResult);
    }
}
