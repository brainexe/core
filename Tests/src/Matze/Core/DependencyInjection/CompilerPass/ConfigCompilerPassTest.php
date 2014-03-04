<?php

namespace Matze\Tests\Core\DependencyInjection\CompilerPass;

use Matze\Core\DependencyInjection\CompilerPass\ConfigCompilerPass;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConfigCompilerPassTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var ConfigCompilerPass
	 */
	private $_subject;

	/**
	 * @var ContainerBuilder|PHPUnit_Framework_MockObject_MockObject $container
	 */
	private $_mock_container;

	public function setUp() {
		$this->_subject = new ConfigCompilerPass();
		$this->_mock_container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testProcessWithInvalidRoot() {
		$this->_subject->process($this->_mock_container);
	}

} 
