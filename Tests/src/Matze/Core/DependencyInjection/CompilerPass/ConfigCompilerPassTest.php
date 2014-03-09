<?php

namespace Matze\Tests\Core\DependencyInjection\CompilerPass;

use Matze\Core\DependencyInjection\CompilerPass\ConfigCompilerPass;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class ConfigCompilerPassTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var ConfigCompilerPass
	 */
	private $_subject;

	/**
	 * @var ContainerBuilder|PHPUnit_Framework_MockObject_MockObject $container
	 */
	private $_mock_container;

	/**
	 * @var ParameterBag
	 */
	private $_mock_parameter_bag;

	public function setUp() {
		$this->_subject = new ConfigCompilerPass();
		$this->_mock_container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
		$this->_mock_parameter_bag = $this->getMock('Symfony\Component\DependencyInjection\ParameterBag\ParameterBag');
	}

	public function testProcessWithInvalidRoot() {
		$this->_mock_container
			->expects($this->once())
			->method('getParameterBag')
			->will($this->returnValue($this->_mock_parameter_bag));

		$this->_subject->process($this->_mock_container);
	}

} 
