<?php

namespace BrainExe\Tests\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\TwigExtensionCompilerPass;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Twig_Extension_Debug;
use Twig_Loader_Array;

class TwigExtensionCompilerPassTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var TwigExtensionCompilerPass
	 */
	private $_subject;

	/**
	 * @var ContainerBuilder|PHPUnit_Framework_MockObject_MockObject $container
	 */
	private $_mockContainer;

	/**
	 * @var Definition|PHPUnit_Framework_MockObject_MockObject $container
	 */
	private $_mockTwig;

	/**
	 * @var Definition|PHPUnit_Framework_MockObject_MockObject $container
	 */
	private $_mockTwigCompiler;

	public function setUp() {
		$this->_subject = new TwigExtensionCompilerPass();

		$this->_mockContainer = $this->getMock(ContainerBuilder::class);
		$this->_mockTwig = $this->getMock(Definition::class);
		$this->_mockTwigCompiler = $this->getMock(Definition::class);
	}

	public function testProcessCompiler() {
		$service_id = 'FooExtension';

		$mock_extension_definition = $this->getMock(Definition::class);

		$this->_mockContainer
			->expects($this->at(0))
			->method('getDefinition')
			->with('Twig')
			->will($this->returnValue($this->_mockTwig));

		$this->_mockContainer
			->expects($this->at(1))
			->method('getDefinition')
			->with('TwigCompiler')
			->will($this->returnValue($this->_mockTwigCompiler));

		$this->_mockContainer
			->expects($this->at(2))
			->method('findTaggedServiceIds')
			->with(TwigExtensionCompilerPass::TAG)
			->will($this->returnValue([$service_id => [['compiler' => 0]]]));

		$this->_mockContainer
			->expects($this->at(3))
			->method('getParameter')
			->with('debug')
			->will($this->returnValue(true));

		$this->_mockContainer
			->expects($this->at(4))
			->method('getDefinition')
			->with($service_id)
			->will($this->returnValue($mock_extension_definition));

		$mock_extension_definition
			->expects($this->once())
			->method('setPublic')
			->with(false);

		$this->_mockTwig
			->expects($this->at(0))
			->method('setArguments')
			->with([new Definition(Twig_Loader_Array::class, [[]])]);

		$this->_mockTwig
			->expects($this->at(1))
			->method('addMethodCall')
			->with('addExtension', [new Reference($service_id)]);

		$this->_mockTwig
			->expects($this->at(2))
			->method('addMethodCall')
			->with('addExtension', [new Definition(Twig_Extension_Debug::class)]);

		$this->_mockTwig
			->expects($this->at(3))
			->method('addMethodCall')
			->with('enableStrictVariables');

		$this->_subject->process($this->_mockContainer);
	}

}
