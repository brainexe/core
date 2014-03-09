<?php

namespace Matze\Tests\Core\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Definition;
use Matze\Core\DependencyInjection\CompilerPass\ControllerCompilerPass;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ControllerCompilerPassTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var ControllerCompilerPass
	 */
	private $_subject;

	/**
	 * @var ContainerBuilder|PHPUnit_Framework_MockObject_MockObject $container
	 */
	private $_mock_container;

	/**
	 * @var Definition|PHPUnit_Framework_MockObject_MockObject $container
	 */
	private $_mock_router_definition;

	public function setUp() {
		$this->_subject = new ControllerCompilerPass();
		$this->_mock_container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
		$this->_mock_router_definition = $this->getMock('Symfony\Component\DependencyInjection\Definition');
	}

	public function testAddSubscriber() {
		$this->_mock_container
			->expects($this->once())
			->method('getDefinition')
			->with('RouteCollection')
			->will($this->returnValue($this->_mock_router_definition));

		//TODO 
		$this->_subject->process($this->_mock_container);
	}

} 
