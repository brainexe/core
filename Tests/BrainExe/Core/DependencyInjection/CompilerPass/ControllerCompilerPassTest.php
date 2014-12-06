<?php

namespace BrainExe\Tests\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Controller\ControllerInterface;
use BrainExe\Core\DependencyInjection\CompilerPass\ControllerCompilerPass;
use PHPUnit_Framework_MockObject_MockObject as MockObject ;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ControllerCompilerPassTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var ControllerCompilerPass|MockObject
	 */
	private $_subject;

	/**
	 * @var ContainerBuilder|MockObject $container
	 */
	private $_mockContainer;

	/**
	 * @var Definition|MockObject $container
	 */
	private $_mockRouterDefinition;

	public function setUp() {
		$this->_subject = $this->getMock(ControllerCompilerPass::class, ['_dumpMatcher']);

		$this->_mockContainer        = $this->getMock(ContainerBuilder::class);
		$this->_mockRouterDefinition = $this->getMock(Definition::class);
	}

	public function testProcess() {
		$route_1 = new Route([]);
		$route_2 = new Route([]);

		$route_1->setCsrf(true);

		$service = $this->getMock(Definition::class);
		$service_ids = [
			$service_id = 'service_id' => [
				[$route_1],
				[$route_2],
			]
		];

		$this->_mockContainer
			->expects($this->at(0))
			->method('getDefinition')
			->with('RouteCollection')
			->willReturn($this->_mockRouterDefinition);

		$this->_mockContainer
			->expects($this->at(1))
			->method('findTaggedServiceIds')
			->with(ControllerCompilerPass::ROUTE_TAG)
			->willReturn($service_ids);

		$this->_mockContainer
			->expects($this->at(2))
			->method('getDefinition')
			->with($service_id)
			->willReturn($service);

		$service
			->expects($this->once())
			->method('clearTag')
			->with(ControllerCompilerPass::ROUTE_TAG);

		$this->_subject
			->expects($this->once())
			->method('_dumpMatcher')
			->with($this->_mockContainer);

		$this->_subject->process($this->_mockContainer);
	}
}
