<?php

namespace Tests\BrainExe\Core\DependencyInjection\CompilerPass\TestCompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\MessageQueueTestService;
use BrainExe\Core\DependencyInjection\CompilerPass\TestCompilerPass;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @Covers BrainExe\Core\DependencyInjection\CompilerPass\TestCompilerPass
 */
class TestCompilerPassTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var TestCompilerPass
	 */
	private $_subject;

	/**
	 * @var ContainerBuilder|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockContainer;

	public function setUp() {
		$this->_mockContainer = $this->getMock(ContainerBuilder::class);

		$this->_subject = new TestCompilerPass();
	}

	public function testProcess() {
		$definition_1 = $this->getMock(Definition::class);
		$definition_2 = $this->getMock(Definition::class);

		$this->_mockContainer
			->expects($this->once())
			->method('getDefinitions')
			->will($this->returnValue([$definition_1, $definition_2]));

		$definition_1
			->expects($this->once())
			->method('setPublic')
			->with(true);

		$definition_2
			->expects($this->once())
			->method('setPublic')
			->with(true);

		$this->_mockContainer
			->expects($this->once())
			->method('set')
			->with(MessageQueueTestService::ID, $this->isInstanceOf(MessageQueueTestService::class));
		
		$this->_subject->process($this->_mockContainer);
	}

}
