<?php

namespace Tests\BrainExe\Core\Application\SelfUpdate\SelfUpdate;

use BrainExe\Core\Application\SelfUpdate\SelfUpdateEvent;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use BrainExe\Core\Application\SelfUpdate\SelfUpdate;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use BrainExe\Core\EventDispatcher\EventDispatcher;

/**
 * @Covers BrainExe\Core\Application\SelfUpdate\SelfUpdate
 */
class SelfUpdateTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var SelfUpdate
	 */
	private $_subject;

	/**
	 * @var ProcessBuilder|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockProcessBuilder;

	/**
	 * @var EventDispatcher|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockEventDispatcher;

	public function setUp() {
		$this->_mockProcessBuilder = $this->getMock(ProcessBuilder::class, [], [], '', false);
		$this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

		$this->_subject = new SelfUpdate($this->_mockProcessBuilder);
		$this->_subject->setEventDispatcher($this->_mockEventDispatcher);
	}

	public function testStartUpdate() {
		$process = $this->getMock(Process::class, [], [], '', false);

		$this->_mockProcessBuilder
			->expects($this->once())
			->method('setArguments')
			->will($this->returnValue($this->_mockProcessBuilder));

		$this->_mockProcessBuilder
			->expects($this->once())
			->method('setTimeout')
			->with(0)
			->will($this->returnValue($this->_mockProcessBuilder));

		$this->_mockProcessBuilder
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

		$this->_mockEventDispatcher
			->expects($this->once())
			->method('dispatchEvent')
			->with($event);

		$this->_subject->startUpdate();
	}

}
