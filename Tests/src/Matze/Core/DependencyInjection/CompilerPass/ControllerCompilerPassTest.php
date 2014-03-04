<?php

namespace Matze\Tests\Core\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Definition;
use Matze\Core\DependencyInjection\CompilerPass\ControllerCompilerPass;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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
		$service_id = 'FooController';
		$routes = [
			$route_1_id = 'route_1' => [
				'pattern' => $pattern_1 = 'pattern_1',
				'defaults' => $default_1 = 'defaults_1',
			],
			$route_2_id = 'route_2' => [
				'pattern' => $pattern_2 = 'pattern_2',
				'defaults' => $default_2 = 'defaults_2',
				'requirements' => $requirements_2 = 'requirements_2',
			]
		];

		$mock_controller = $this->getMock('Matze\Core\Controller\ControllerInterface');

		$this->_mock_container
			->expects($this->once())
			->method('findTaggedServiceIds')
			->with(ControllerCompilerPass::TAG)
			->will($this->returnValue([$service_id => []]));

		$this->_mock_container
			->expects($this->once())
			->method('getDefinition')
			->with('RouteCollection')
			->will($this->returnValue($this->_mock_router_definition));

		$this->_mock_container
			->expects($this->once())
			->method('get')
			->with($service_id)
			->will($this->returnValue($mock_controller));

		$mock_controller
			->expects($this->once())
			->method('getRoutes')
			->will($this->returnValue($routes));

		$this->_mock_router_definition
			->expects($this->at(0))
			->method('addMethodCall')
			->with('add', [$route_1_id, new Definition('Symfony\Component\Routing\Route', [$pattern_1, $default_1])]);

		$this->_mock_router_definition
			->expects($this->at(1))
			->method('addMethodCall')
			->with('add', [$route_2_id, new Definition('Symfony\Component\Routing\Route', [$pattern_2, $default_2, $requirements_2])]);

		$this->_subject->process($this->_mock_container);
	}

} 
