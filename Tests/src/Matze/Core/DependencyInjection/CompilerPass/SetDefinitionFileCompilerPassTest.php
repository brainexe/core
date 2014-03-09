<?php

namespace Matze\Tests\Core\DependencyInjection\CompilerPass;

use Matze\Core\DependencyInjection\CompilerPass\SetDefinitionFileCompilerPass;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class FooTestClass {

}

class SetDefinitionFileCompilerPassTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var SetDefinitionFileCompilerPass
	 */
	private $_subject;

	/**
	 * @var ContainerBuilder|PHPUnit_Framework_MockObject_MockObject $container
	 */
	private $_mock_container;

	/**
	 * @var Definition|PHPUnit_Framework_MockObject_MockObject $container
	 */
	private $_mock_definition;

	public function setUp() {
		$this->_subject = new SetDefinitionFileCompilerPass();
		$this->_mock_container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
		$this->_mock_definition = $this->getMock('Symfony\Component\DependencyInjection\Definition');
	}

	public function testProcessCompilerWithInvalidDefinition() {
		$service_id = 'FooService';

		$this->_mock_container
			->expects($this->once())
			->method('getServiceIds')
			->will($this->returnValue([$service_id]));

		$this->_mock_container
			->expects($this->once())
			->method('hasDefinition')
			->with($service_id)
			->will($this->returnValue(false));

		$this->_subject->process($this->_mock_container);
	}

	public function testProcessCompiler() {
		$service_id = 'FooService';

		$this->_mock_container
			->expects($this->once())
			->method('getServiceIds')
			->will($this->returnValue([$service_id]));

		$this->_mock_container
			->expects($this->once())
			->method('hasDefinition')
			->with($service_id)
			->will($this->returnValue(true));

		$this->_mock_definition
			->expects($this->once())
			->method('getClass')
			->will($this->returnValue('Matze\Tests\Core\DependencyInjection\CompilerPass\FooTestClass'));

		$this->_mock_definition
			->expects($this->once())
			->method('setFile')
			->with(__FILE__);

		$this->_mock_container
			->expects($this->once())
			->method('getDefinition')
			->with($service_id)
			->will($this->returnValue($this->_mock_definition));

		$this->_subject->process($this->_mock_container);
	}

	public function testProcessCompilerWithInvalidFile() {
		$service_id = 'FooService';

		$this->_mock_container
			->expects($this->once())
			->method('getServiceIds')
			->will($this->returnValue([$service_id]));

		$this->_mock_container
			->expects($this->once())
			->method('hasDefinition')
			->with($service_id)
			->will($this->returnValue(true));

		$this->_mock_definition
			->expects($this->once())
			->method('getClass')
			->will($this->returnValue('InvalidClass'));

		$this->_mock_definition
			->expects($this->never())
			->method('setFile');

		$this->_mock_container
			->expects($this->once())
			->method('getDefinition')
			->with($service_id)
			->will($this->returnValue($this->_mock_definition));

		$this->_subject->process($this->_mock_container);
	}

} 
