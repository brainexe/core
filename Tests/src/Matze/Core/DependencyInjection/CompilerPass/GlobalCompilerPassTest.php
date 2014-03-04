<?php

namespace Matze\Tests\Core\DependencyInjection\CompilerPass;

use Matze\Core\DependencyInjection\CompilerPass\GlobalCompilerPass;

class GlobalCompilerPassTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var GlobalCompilerPass
	 */
	private $_subject;

	/**
	 * @var ContainerBuilder|PHPUnit_Framework_MockObject_MockObject $container
	 */
	private $_mock_container;

	public function setUp() {
		$this->_subject = new GlobalCompilerPass();
		$this->_mock_container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
	}

	public function testProcessCompiler() {
		$service_id = 'FooCompilerPass';

		$compiler_mock = $this->getMock('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface');

		$this->_mock_container
			->expects($this->once())
			->method('findTaggedServiceIds')
			->with(GlobalCompilerPass::TAG)
			->will($this->returnValue([$service_id => [['priority' => $priority = 10]]]));

		$this->_mock_container
			->expects($this->once())
			->method('get')
			->with($service_id)
			->will($this->returnValue($compiler_mock));

		$compiler_mock
			->expects($this->once())
			->method('process')
			->with($this->_mock_container);

		$this->_subject->process($this->_mock_container);
	}

} 
