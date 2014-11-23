<?php

namespace Tests\BrainExe\Core\DependencyInjection\CompilerPass\MiddlewareCompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\MiddlewareCompilerPass;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @Covers BrainExe\Core\DependencyInjection\CompilerPass\MiddlewareCompilerPass
 */
class MiddlewareCompilerPassTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var MiddlewareCompilerPass
	 */
	private $_subject;

	/**
	 * @var ContainerBuilder|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockContainer;

	public function setUp() {
		$this->_mockContainer = $this->getMock(ContainerBuilder::class);

		$this->_subject = new MiddlewareCompilerPass();

	}

	public function testProcess() {
		$app_kernel = $this->getMock(Definition::class);

		$service_ids = [
			$service_id_1 = 'service_id1' => [0 => ['priority' => 5]],
			$service_id_2 = 'service_id2' => [0 => ['priority' => null]],
		];

		$this->_mockContainer
			->expects($this->once())
			->method('findTaggedServiceIds')
			->with(MiddlewareCompilerPass::TAG)
			->will($this->returnValue($service_ids));

		$this->_mockContainer
			->expects($this->once())
			->method('getDefinition')
			->with('AppKernel')
			->will($this->returnValue($app_kernel));

		$app_kernel
			->expects($this->once())
			->method('addMethodCall')
			->with('setMiddlewares', [[new Reference($service_id_1)]]);

		$this->_subject->process($this->_mockContainer);
	}

}
