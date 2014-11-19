<?php

namespace BrainExe\Tests\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\ControllerCompilerPass;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ControllerCompilerPassTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var ControllerCompilerPass
	 */
	private $_subject;

	/**
	 * @var ContainerBuilder|PHPUnit_Framework_MockObject_MockObject $container
	 */
	private $_mockContainer;

	/**
	 * @var Definition|PHPUnit_Framework_MockObject_MockObject $container
	 */
	private $_mockRouterDefinition;

	public function setUp() {
		$this->_subject = new ControllerCompilerPass();

		$this->_mockContainer = $this->getMock(ContainerBuilder::class);
		$this->_mockRouterDefinition = $this->getMock(Definition::class);
	}

	public function testProcess() {
		$this->markTestIncomplete();

		$this->_mockContainer
			->expects($this->once())
			->method('getDefinition')
			->with('RouteCollection')
			->will($this->returnValue($this->_mockRouterDefinition));

		$this->_subject->process($this->_mockContainer);
	}
} 
