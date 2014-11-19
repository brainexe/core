<?php

namespace BrainExe\Tests\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\ConfigCompilerPass;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\ParameterBag;

class ConfigCompilerPassTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var ConfigCompilerPass
	 */
	private $_subject;

	/**
	 * @var ContainerBuilder|PHPUnit_Framework_MockObject_MockObject $container
	 */
	private $_mockContainer;

	/**
	 * @var ParameterBag
	 */
	private $_mockParameterBag;

	public function setUp() {
		$this->_subject = new ConfigCompilerPass();

		$this->_mockContainer = $this->getMock(ContainerBuilder::class);
		$this->_mockParameterBag = $this->getMock(ParameterBag::class);
	}

	public function testProcessWithInvalidRoot() {
		$this->_mockContainer
			->expects($this->once())
			->method('getParameterBag')
			->will($this->returnValue($this->_mockParameterBag));

		$this->_subject->process($this->_mockContainer);
	}

} 
