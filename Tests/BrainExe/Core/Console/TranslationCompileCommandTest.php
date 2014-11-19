<?php

namespace Tests\BrainExe\Core\Console\TranslationCompileCommand;

use BrainExe\Core\Console\TranslationCompileCommand;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @Covers BrainExe\Core\Console\TranslationCompileCommand
 */
class TranslationCompileCommandTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var TranslationCompileCommand
	 */
	private $_subject;

	public function setUp() {
		$this->_subject = new TranslationCompileCommand();
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
