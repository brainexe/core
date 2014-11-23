<?php

namespace Tests\BrainExe\Core\Application\SelfUpdate\SelfUpdateCommand;

use BrainExe\Core\Application\SelfUpdate\SelfUpdate;
use BrainExe\Core\Application\SelfUpdate\SelfUpdateCommand;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @Covers BrainExe\Core\Application\SelfUpdate\SelfUpdateCommand
 */
class SelfUpdateCommandTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var SelfUpdateCommand
	 */
	private $_subject;

	/**
	 * @var SelfUpdate|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockSelfUpdate;

	/**
	 * @var EventDispatcher|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockEventDispatcher;

	public function setUp() {
		$this->_mockSelfUpdate = $this->getMock(SelfUpdate::class, [], [], '', false);
		$this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

		$this->_subject = new SelfUpdateCommand($this->_mockSelfUpdate, $this->_mockEventDispatcher);
	}

	public function testExecute() {
		$application = new Application();
		$application->add($this->_subject);

		$commandTester = new CommandTester($this->_subject);

		$this->_mockSelfUpdate
			->expects($this->once())
			->method('startUpdate');

		$commandTester->execute([]);
	}

}
