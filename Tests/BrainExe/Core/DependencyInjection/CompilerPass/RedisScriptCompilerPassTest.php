<?php

namespace Tests\BrainExe\Core\DependencyInjection\CompilerPass\RedisScriptCompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\RedisScriptCompilerPass;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @Covers BrainExe\Core\DependencyInjection\CompilerPass\RedisScriptCompilerPass
 */
class RedisScriptCompilerPassTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var RedisScriptCompilerPass
	 */
	private $_subject;


	public function setUp() {


		$this->_subject = new RedisScriptCompilerPass();

	}

	public function testProcess() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$container = new ContainerBuilder();
		$this->_subject->process($container);
	}

}
