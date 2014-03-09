<?php

namespace Matze\Tests\Core\DependencyInjection\CompilerPass;

use Matze\Core\DependencyInjection\CompilerPass\LoggerCompilerPass;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class LoggerCompilerPassTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var LoggerCompilerPass
	 */
	private $_subject;

	/**
	 * @var ContainerBuilder|PHPUnit_Framework_MockObject_MockObject $container
	 */
	private $_mock_container;

	/**
	 * @var Definition|PHPUnit_Framework_MockObject_MockObject $container
	 */
	private $_mock_logger_definition;

	public function setUp() {
		$this->_subject = new LoggerCompilerPass();
		$this->_mock_container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
		$this->_mock_logger_definition = $this->getMock('Symfony\Component\DependencyInjection\Definition');
	}

	public function testProcessCompiler() {
		$this->_mock_container
			->expects($this->once())
			->method('getParameter')
			->with('debug')
			->will($this->returnValue(true));

		$this->_mock_container
			->expects($this->once())
			->method('getDefinition')
			->with('monolog.Logger')
			->will($this->returnValue($this->_mock_logger_definition));

		$this->_subject->process($this->_mock_container);
	}

} 
