<?php

use BrainExe\Core\Traits\TimeTrait;
use BrainExe\Core\Util\Time;

class TimeTraitTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var TimeTrait
	 */
	private $_subject;

	/**
	 * @var Time
	 */
	private $_mockTime;

	public function setUp() {
		$this->_mockTime = $this->getMock(Time::class);

		$this->_subject = $this->getMockForTrait(TimeTrait::class);
		$this->_subject->setTime($this->_mockTime);
	}

	public function testGetTime() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->now();
	}
}