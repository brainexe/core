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
		$application = new Application();
		$application->add($this->_subject);

		$commandTester = new CommandTester($this->_subject);

		$token = 11880;

		$this->_mockRegisterTokens
			->expects($this->once())
			->method('addToken')
			->will($this->returnValue($token));

		$commandTester->execute([]);
		$output = $commandTester->getDisplay();

		$expected_result = sprintf("/register/?token=%s\n", $token);
		$this->assertEquals($expected_result, $output);
	}

}
