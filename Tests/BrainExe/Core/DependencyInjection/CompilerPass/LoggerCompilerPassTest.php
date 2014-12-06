<?php

namespace BrainExe\Tests\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\LoggerCompilerPass;
use Monolog\Handler\ChromePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class LoggerCompilerPassTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var LoggerCompilerPass
	 */
	private $_subject;

	/**
	 * @var ContainerBuilder|MockObject $container
	 */
	private $_mock_container;

	/**
	 * @var Definition|MockObject $container
	 */
	private $_mockLoggerDefinition;

	public function setUp() {
		$this->_subject = new LoggerCompilerPass();

		$this->_mock_container = $this->getMock(ContainerBuilder::class);
		$this->_mockLoggerDefinition = $this->getMock(Definition::class);
	}

	public function testProcessCompilerWithCoreStandalone() {
		$this->_mock_container
			->expects($this->once())
			->method('getParameter')
			->with('core_standalone')
			->will($this->returnValue(true));

		$this->_mock_container
			->expects($this->once())
			->method('getDefinition')
			->with('monolog.Logger')
			->will($this->returnValue($this->_mockLoggerDefinition));

		$this->_subject->process($this->_mock_container);
	}

	public function testProcessCompilerWitDebug() {
		$this->_mock_container
			->expects($this->at(0))
			->method('getDefinition')
			->with('monolog.Logger')
			->will($this->returnValue($this->_mockLoggerDefinition));

		$this->_mock_container
			->expects($this->at(1))
			->method('getParameter')
			->with('core_standalone')
			->will($this->returnValue(false));

		$this->_mock_container
			->expects($this->at(2))
			->method('getParameter')
			->with('debug')
			->will($this->returnValue(true));

		$this->_mockLoggerDefinition
			->expects($this->at(0))
			->method('addMethodCall')
			->with('pushHandler', [new Definition(ChromePHPHandler::class)]);

		$this->_mockLoggerDefinition
			->expects($this->at(1))
			->method('addMethodCall')
			->with('pushHandler', [new Definition(StreamHandler::class, ['php://stdout', Logger::INFO])]);

		$this->_subject->process($this->_mock_container);
	}

}
