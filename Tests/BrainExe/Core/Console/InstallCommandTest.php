<?php

namespace Tests\BrainExe\Core\Console\InstallCommand;

use BrainExe\Core\Console\InstallCommand;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Application;
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
