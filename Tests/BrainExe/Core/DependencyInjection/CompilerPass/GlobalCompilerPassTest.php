<?php

namespace BrainExe\Tests\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\GlobalCompilerPass;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Component\DependencyInjection\ContainerBuilder;

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
		$logger_mock = $this->getMock('Monolog\Logger', [], [], '', false);

		$this->_mock_container
			->expects($this->at(0))
			->method('setParameter');

		$this->_mock_container
			->expects($this->at(1))
			->method('setParameter');

		$this->_mock_container
			->expects($this->at(2))
			->method('findTaggedServiceIds')
			->with(GlobalCompilerPass::TAG)
			->will($this->returnValue([$service_id => [['priority' => $priority = 10]]]));

		$this->_mock_container
			->expects($this->at(3))
			->method('get')
			->with($service_id)
			->will($this->returnValue($compiler_mock));

		$this->_mock_container
			->expects($this->at(4))
			->method('get')
			->with('monolog.logger')
			->will($this->returnValue($logger_mock));

		$compiler_mock
			->expects($this->once())
			->method('process')
			->with($this->_mock_container);

		$this->_subject->process($this->_mock_container);
	}

} 
