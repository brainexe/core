<?php

namespace Matze\Tests\Core\DependencyInjection\CompilerPass;

use Matze\Core\DependencyInjection\CompilerPass\TwigExtensionCompilerPass;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;

class TwigExtensionCompilerPassTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var TwigExtensionCompilerPass
	 */
	private $_subject;

	/**
	 * @var ContainerBuilder|PHPUnit_Framework_MockObject_MockObject $container
	 */
	private $_mock_container;

	/**
	 * @var Definition|PHPUnit_Framework_MockObject_MockObject $container
	 */
	private $_mock_twig_definition;

	public function setUp() {
		$this->_subject = new TwigExtensionCompilerPass();
		$this->_mock_container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
		$this->_mock_twig_definition = $this->getMock('Symfony\Component\DependencyInjection\Definition');
	}

	public function testProcessCompiler() {
		$service_id = 'FooExtension';

		$mock_extension_definition = $this->getMock('Symfony\Component\DependencyInjection\Definition');

		$this->_mock_container
			->expects($this->at(0))
			->method('getDefinition')
			->with('Twig')
			->will($this->returnValue($this->_mock_twig_definition));

		$this->_mock_container
			->expects($this->at(1))
			->method('findTaggedServiceIds')
			->with(TwigExtensionCompilerPass::TAG)
			->will($this->returnValue([$service_id => []]));

		$this->_mock_container
			->expects($this->at(2))
			->method('getDefinition')
			->with($service_id)
			->will($this->returnValue($mock_extension_definition));

		$mock_extension_definition
			->expects($this->once())
			->method('setPublic')
			->with(false);

		$this->_mock_twig_definition
			->expects($this->at(0))
			->method('addMethodCall')
			->with('addExtension', [new Reference($service_id)]);

		$this->_mock_container
			->expects($this->at(3))
			->method('getParameter')
			->with('debug')
			->will($this->returnValue(true));

		$this->_mock_twig_definition
			->expects($this->at(1))
			->method('addMethodCall')
			->with('addExtension', [new Definition('Twig_Extension_Debug')]);

		$this->_mock_twig_definition
			->expects($this->at(2))
			->method('addMethodCall')
			->with('enableStrictVariables');

		$this->_subject->process($this->_mock_container);
	}

} 
