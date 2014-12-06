<?php

use BrainExe\Core\Traits\TimeTrait;
use BrainExe\Core\Util\Time;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class TimeTraitTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var TimeTrait
	 */
	private $_subject;

	/**
	 * @var Time|MockObject
	 */
	private $_mockTime;

	public function setUp() {
		$this->_mockTime = $this->getMock(Time::class);

		$this->_subject = $this->getMockForTrait(TimeTrait::class);
		$this->_subject->setTime($this->_mockTime);
	}

	public function testGetTime() {
		$now = 100;

		$this->_mockTime
			->expects($this->once())
			->method('now')
			->will($this->returnValue($now));

		$actual_result = $this->_subject->now();

		$this->assertEquals($now, $actual_result);
	}
}
