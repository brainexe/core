<?php

namespace Tests\BrainExe\Core\Console\CreateUserCommand;

use BrainExe\Core\Authentication\Register;
use BrainExe\Core\Console\CreateUserCommand;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @Covers BrainExe\Core\Console\CreateUserCommand
 */
class CreateUserCommandTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var CreateUserCommand
	 */
	private $_subject;

	/**
	 * @var Register|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockRegister;

	public function setUp() {
		$this->_mockRegister = $this->getMock(Register::class, [], [], '', false);

		$this->_subject = new CreateUserCommand($this->_mockRegister);
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
