<?php

namespace Tests\BrainExe\Core\Application\SelfUpdate\SelfUpdateListener;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use BrainExe\Core\Application\SelfUpdate\SelfUpdateListener;
use BrainExe\Core\Application\SelfUpdate\SelfUpdate;

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
		parent::setUp();

		$this->_mockSelfUpdate = $this->getMock(SelfUpdate::class, [], [], '', false);

		$this->_subject = new SelfUpdateListener($this->_mockSelfUpdate);

	}

	public function testGetSubscribedEvents() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->getSubscribedEvents();
	}

	public function testStartSelfUpdate() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->startSelfUpdate();
	}

}
