<?php

namespace Tests\BrainExe\Core\Application\SelfUpdate\SelfUpdateListener;

use BrainExe\Core\Application\SelfUpdate\SelfUpdate;
use BrainExe\Core\Application\SelfUpdate\SelfUpdateListener;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

/**
 * @Covers BrainExe\Core\Application\SelfUpdate\SelfUpdateListener
 */
class SelfUpdateListenerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var SelfUpdateListener
	 */
	private $_subject;

	/**
	 * @var SelfUpdate|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockSelfUpdate;

	public function setUp() {
		$this->_mockSelfUpdate = $this->getMock(SelfUpdate::class, [], [], '', false);

		$this->_subject = new SelfUpdateListener($this->_mockSelfUpdate);
	}

	public function testGetSubscribedEvents() {
		$actual_result = $this->_subject->getSubscribedEvents();
		$this->assertInternalType('array', $actual_result);
	}

	public function testStartSelfUpdate() {
		$this->_mockSelfUpdate
			->expects($this->once())
			->method('startUpdate');

		$this->_subject->startSelfUpdate();
	}

}
