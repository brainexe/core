<?php

namespace Tests\BrainExe\Core\DependencyInjection\CompilerPass\RedisCompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\RedisCompilerPass;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @Covers BrainExe\Core\DependencyInjection\CompilerPass\RedisCompilerPass
 */
class RedisCompilerPassTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var RedisCompilerPass
	 */
	private $_subject;

	public function setUp() {
		$this->_subject = new RedisCompilerPass();
	}

	public function testProcess() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$container = new ContainerBuilder();
		$this->_subject->process($container);
	}

}
