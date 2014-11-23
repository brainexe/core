<?php

namespace Tests\BrainExe\Core\Console\CompileTemplatesCommand;

use BrainExe\Core\Console\CompileTemplatesCommand;
use BrainExe\Template\TwigEnvironment;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @Covers BrainExe\Core\Console\CompileTemplatesCommand
 */
class CompileTemplatesCommandTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var CompileTemplatesCommand
	 */
	private $_subject;

	/**
	 * @var TwigEnvironment|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockTwigEnvironment;

	public function setUp() {
		$this->_mockTwigEnvironment = $this->getMock(TwigEnvironment::class, [], [], '', false);

		$this->_subject = new CompileTemplatesCommand('/www/brainexe/core/templates/', $this->_mockTwigEnvironment);
	}

	public function testExecute() {
		$application = new Application();
		$application->add($this->_subject);

		$commandTester = new CommandTester($this->_subject);

		$commandTester->execute([]);
	}

}
