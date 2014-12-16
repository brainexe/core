<?php

namespace BrainExe\Tests\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\ConfigCompilerPass;
use BrainExe\Core\Util\FileSystem;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\ParameterBag;

class ConfigCompilerPassTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var ConfigCompilerPass
	 */
	private $_subject;

	/**
	 * @var ContainerBuilder|MockObject $container
	 */
	private $_mockContainer;

	/**
	 * @var ParameterBag|MockObject
	 */
	private $_mockParameterBag;

	/**
	 * @var Finder|MockObject
	 */
	private $_mockFinder;

	/**
	 * @var FileSystem|MockObject
	 */
	private $_mockFileSystem;

	public function setUp() {
		$this->_mockContainer = $this->getMock(ContainerBuilder::class);
		$this->_mockParameterBag = $this->getMock(ParameterBag::class);
		$this->_mockFileSystem = $this->getMock(FileSystem::class);
		$this->_mockFinder = $this->getMock(Finder::class, [], [], '', false);

		$this->_subject = new ConfigCompilerPass();
	}

	public function testProcessWithInvalidRoot() {
		$this->markTestIncomplete();
		$this->_mockContainer
			->expects($this->once())
			->method('setParameter')
			->with('core_standalone');

		$this->_mockFinder
			->expects($this->once())
			->method('files')
			->willReturnSelf();
		$this->_mockFinder
			->expects($this->once())
			->method('depth')
			->willReturnSelf();
		$this->_mockFinder
			->expects($this->once())
			->method('in')
			->willReturnSelf();
		$this->_mockFinder
			->expects($this->once())
			->method('name')
			->willReturnSelf();

		$this->_mockFileSystem
			->expects($this->once())
			->method('exists')
			->with(ROOT . 'app')
			->willReturn(false);

		$this->_subject->process($this->_mockContainer);
	}

}
