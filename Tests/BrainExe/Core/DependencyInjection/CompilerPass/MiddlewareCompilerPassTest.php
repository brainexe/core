<?php

namespace Tests\BrainExe\Core\DependencyInjection\CompilerPass\MiddlewareCompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\MiddlewareCompilerPass;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @Covers BrainExe\Core\DependencyInjection\CompilerPass\MiddlewareCompilerPass
 */
class MiddlewareCompilerPassTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var MiddlewareCompilerPass
	 */
	private $_subject;


	public function setUp() {


		$this->_subject = new MiddlewareCompilerPass();

	}

	public function testProcess() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$container = new ContainerBuilder();
		$this->_subject->process($container);
	}

}
