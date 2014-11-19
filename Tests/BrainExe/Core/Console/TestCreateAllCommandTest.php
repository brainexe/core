<?php

namespace Tests\Ig\StratCity\Classes\System\Commands\Test\TestCreateAllCommand;

use BrainExe\Core\Console\TestCreateAllCommand;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class TestCreateAllCommandTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var TestCreateAllCommand
	 */
	private $_subject;

	public function setUp() {
		$this->_subject = new TestCreateAllCommand();
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
