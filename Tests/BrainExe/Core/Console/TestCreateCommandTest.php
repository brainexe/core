<?php

namespace Tests\Ig\StratCity\Classes\System\Commands\Test\TestCreateCommand;

use BrainExe\Core\Console\TestCreateCommand;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\Container;

class TestCreateCommandTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var TestCreateCommand
	 */
	private $_subject;

	/**
	 * @var Container|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockContainer;

	public function setUp() {
		$this->_mockContainer = $this->getMock(Container::class, [], [], '', false);

		$this->_subject = new TestCreateCommand();
		$this->_subject->setContainer($this->_mockContainer);
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
