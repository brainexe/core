<?php

namespace Tests\BrainExe\Core\Application\SelfUpdate\SelfUpdate;

use BrainExe\Core\Application\SelfUpdate\SelfUpdate;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

/**
 * @Covers BrainExe\Core\Application\SelfUpdate\SelfUpdate
 */
class SelfUpdateTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var SelfUpdate
	 */
	private $_subject;

	/**
	 * @var EventDispatcher|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockEventDispatcher;


	public function setUp() {
		parent::setUp();

		$this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

		$this->_subject = new SelfUpdate();
		$this->_subject->setEventDispatcher($this->_mockEventDispatcher);
	}

	public function testStartUpdate() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->startUpdate();
	}

}
