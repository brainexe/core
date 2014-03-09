<?php

namespace Matze\Tests\Core\DependencyInjection\CompilerPass;

use Matze\Core\DependencyInjection\CompilerPass\ConsoleCompilerPass;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ConsoleCompilerPassTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var ConsoleCompilerPass
	 */
	private $_subject;

	/**
	 * @var ContainerBuilder|PHPUnit_Framework_MockObject_MockObject $container
	 */
	private $_mock_container;

	/**
	 * @var Definition|PHPUnit_Framework_MockObject_MockObject $container
	 */
	private $_mock_console_definition;

	public function setUp() {
		$this->_subject = new ConsoleCompilerPass();
		$this->_mock_container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
		$this->_mock_console_definition = $this->getMock('Symfony\Component\DependencyInjection\Definition');
	}

	public function testAddSubscriber() {
		$service_id = 'FooListener';

		$this->_mock_container
			->expects($this->once())
			->method('findTaggedServiceIds')
			->with(ConsoleCompilerPass::TAG)
			->will($this->returnValue([$service_id => []]));

		$this->_mock_container
			->expects($this->once())
			->method('getDefinition')
			->with('Console')
			->will($this->returnValue($this->_mock_console_definition));

		$this->_mock_console_definition
			->expects($this->at(0))
			->method('addMethodCall')
			->with('setAutoExit', [false]);

		$this->_mock_console_definition
			->expects($this->at(1))
			->method('addMethodCall')
			->with('add', [new Reference($service_id)]);

		$this->_subject->process($this->_mock_container);
	}

} 
