<?php

namespace Tests\BrainExe\Core\Console\CreateRegisterLinkCommand;

use BrainExe\Core\Authentication\RegisterTokens;
use BrainExe\Core\Console\CreateRegisterLinkCommand;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @Covers BrainExe\Core\Console\CreateRegisterLinkCommand
 */
class CreateRegisterLinkCommandTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var CreateRegisterLinkCommand
	 */
	private $_subject;

	/**
	 * @var RegisterTokens|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockRegisterTokens;

	public function setUp() {
		$this->_mockRegisterTokens = $this->getMock(RegisterTokens::class, [], [], '', false);

		$this->_subject = new CreateRegisterLinkCommand($this->_mockRegisterTokens);
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
