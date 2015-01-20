<?php

namespace Tests\BrainExe\Core\Application\SelfUpdate\SelfUpdate;

use BrainExe\Core\Application\SelfUpdate\SelfUpdate;
use BrainExe\Core\Application\SelfUpdate\SelfUpdateEvent;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @Covers BrainExe\Core\Application\SelfUpdate\SelfUpdate
 */
class SelfUpdateTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var SelfUpdate
     */
    private $subject;

    /**
     * @var ProcessBuilder|MockObject
     */
    private $mockProcessBuilder;

    /**
     * @var EventDispatcher|MockObject
     */
    private $mockEventDispatcher;

    public function setUp()
    {
        $this->mockProcessBuilder = $this->getMock(ProcessBuilder::class, [], [], '', false);
        $this->mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new SelfUpdate($this->mockProcessBuilder);
        $this->subject->setEventDispatcher($this->mockEventDispatcher);
    }

    public function testStartUpdate()
    {
        $process = $this->getMock(Process::class, [], [], '', false);

        $this->mockProcessBuilder
            ->expects($this->once())
            ->method('setWorkingDirectory')
            ->with(ROOT)
            ->willReturnSelf();

        $this->mockProcessBuilder
            ->expects($this->once())
            ->method('setPrefix')
            ->willReturnSelf();

        $this->mockProcessBuilder
            ->expects($this->once())
            ->method('setArguments')
            ->willReturnSelf();

        $this->mockProcessBuilder
            ->expects($this->once())
            ->method('setTimeout')
            ->with(0)
            ->willReturnSelf();

        $this->mockProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($process);

        $process
            ->expects($this->once())
            ->method('run');

        $process
            ->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(true);

        $event = new SelfUpdateEvent(SelfUpdateEvent::DONE);

        $this->mockEventDispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $this->subject->startUpdate();
    }

    public function testStartUpdateWithError()
    {
        $process = $this->getMock(Process::class, [], [], '', false);

        $this->mockProcessBuilder
            ->expects($this->once())
            ->method('setWorkingDirectory')
            ->with(ROOT)
            ->willReturnSelf();

        $this->mockProcessBuilder
            ->expects($this->once())
            ->method('setPrefix')
            ->willReturnSelf();

        $this->mockProcessBuilder
            ->expects($this->once())
            ->method('setArguments')
            ->willReturnSelf();

        $this->mockProcessBuilder
            ->expects($this->once())
            ->method('setTimeout')
            ->with(0)
            ->willReturnSelf();

        $this->mockProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($process);

        $process
            ->expects($this->once())
            ->method('run');

        $process
            ->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(false);

        $event = new SelfUpdateEvent(SelfUpdateEvent::ERROR);

        $this->mockEventDispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $this->subject->startUpdate();
    }
}
