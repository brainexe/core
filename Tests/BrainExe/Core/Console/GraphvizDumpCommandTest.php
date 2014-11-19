<?php

namespace Tests\BrainExe\Core\Console\GraphvizDumpCommand;

use BrainExe\Core\Console\GraphvizDumpCommand;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @Covers BrainExe\Core\Console\GraphvizDumpCommand
 */
class GraphvizDumpCommandTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var GraphvizDumpCommand
	 */
	private $_subject;

	/**
	 * @var EventDispatcher|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockEventDispatcher;

	public function setUp() {
		$this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

		$this->_subject = new GraphvizDumpCommand();
		$this->_subject->setEventDispatcher($this->_mockEventDispatcher);
	}

	public function testExecute() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$application = new Application();
		$application->add($this->_subject);

		$commandTester = new CommandTester($this->_subject);

		// TODO

		$commandTester->execute([]);
		$output = $commandTester->getDisplay();

		$this->assertEquals("TODO\n", $output);
	}

}
