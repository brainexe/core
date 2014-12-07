<?php

namespace Tests\BrainExe\Core\Application\SelfUpdate\SelfUpdate;

use BrainExe\Core\Application\SelfUpdate\SelfUpdate;
use BrainExe\Core\Application\SelfUpdate\SelfUpdateEvent;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @Covers BrainExe\Core\Application\SelfUpdate\SelfUpdate
 */
class SelfUpdateTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var SelfUpdate
	 */
	private $subject;

	/**
	 * @var ProcessBuilder|PHPUnit_Framework_MockObject_MockObject
	 */
	private $mockProcessBuilder;

	/**
	 * @var EventDispatcher|PHPUnit_Framework_MockObject_MockObject
	 */
	private $mockEventDispatcher;

	public function setUp() {
		$this->mockProcessBuilder = $this->getMock(ProcessBuilder::class, [], [], '', false);
		$this->mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

		$this->subject = new SelfUpdate($this->mockProcessBuilder);
		$this->subject->setEventDispatcher($this->mockEventDispatcher);
	}

	public function testStartUpdate() {
		$process = $this->getMock(Process::class, [], [], '', false);

		$this->mockProcessBuilder
			->expects($this->once())
			->method('setArguments')
			->will($this->returnValue($this->mockProcessBuilder));

		$this->mockProcessBuilder
			->expects($this->once())
			->method('setTimeout')
			->with(0)
			->will($this->returnValue($this->mockProcessBuilder));

		$this->mockProcessBuilder
			->expects($this->once())
			->method('getProcess')
			->will($this->returnValue($process));

		$process
			->expects($this->once())
			->method('run');

		$process
			->expects($this->once())
			->method('isSuccessful')
			->will($this->returnValue(true));

		$event = new SelfUpdateEvent(SelfUpdateEvent::DONE);

		$this->mockEventDispatcher
			->expects($this->once())
			->method('dispatchEvent')
			->with($event);

		$this->subject->startUpdate();
	}

	public function testStartUpdateWithError() {
		$process = $this->getMock(Process::class, [], [], '', false);

		$this->mockProcessBuilder
			->expects($this->once())
			->method('setArguments')
			->will($this->returnValue($this->mockProcessBuilder));

		$this->mockProcessBuilder
			->expects($this->once())
			->method('setTimeout')
			->with(0)
			->will($this->returnValue($this->mockProcessBuilder));

		$this->mockProcessBuilder
			->expects($this->once())
			->method('getProcess')
			->will($this->returnValue($process));

		$process
			->expects($this->once())
			->method('run');

		$process
			->expects($this->once())
			->method('isSuccessful')
			->will($this->returnValue(false));

		$event = new SelfUpdateEvent(SelfUpdateEvent::ERROR);

		$this->mockEventDispatcher
			->expects($this->once())
			->method('dispatchEvent')
			->with($event);

		$this->subject->startUpdate();
	}

}
