<?php

namespace Tests\BrainExe\Core\Console\InstallCommand;

use BrainExe\Core\Console\InstallCommand;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @Covers BrainExe\Core\Console\InstallCommand
 */
class InstallCommandTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var InstallCommand
	 */
	private $_subject;

	public function setUp() {
		$this->_subject = new InstallCommand();
	}

	public function testExecute() {
		/** @var Application|PHPUnit_Framework_MockObject_MockObject $application */
		$application = $this->getMock(Application::class, ['run']);
		$this->_subject->setApplication($application);

		$commandTester = new CommandTester($this->_subject);

		$input = new ArrayInput(['command' => 'cache:clear']);
		$application
			->expects($this->once())
			->method('run')
			->with($input);

		$commandTester->execute([]);
		$output = $commandTester->getDisplay();

		$this->assertEquals("", $output);
	}

}
