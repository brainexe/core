<?php

namespace Tests\BrainExe\Core\DependencyInjection\CompilerPass\RedisCompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\RedisCompilerPass;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @Covers BrainExe\Core\DependencyInjection\CompilerPass\RedisCompilerPass
 */
class RedisCompilerPassTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var RedisCompilerPass
	 */
	private $_subject;

	/**
	 * @var ContainerBuilder|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mock_container;

	public function setUp() {
		$this->_mock_container = $this->getMock(ContainerBuilder::class);

		$this->_subject = new RedisCompilerPass();
	}

	public function testProcess() {
		$password = 'testetst';
		$database = 12;

		$redis = $this->getMock(Definition::class);

		$this->_mock_container
			->expects($this->at(0))
			->method('getDefinition')
			->with('redis')
			->will($this->returnValue($redis));

		$this->_mock_container
			->expects($this->at(1))
			->method('getParameter')
			->with('redis.password')
			->will($this->returnValue($password));

		$this->_mock_container
			->expects($this->at(2))
			->method('getParameter')
			->with('redis.database')
			->will($this->returnValue($database));

		$redis
			->expects($this->at(0))
			->method('addMethodCall')
			->with('auth', [$password]);

		$redis
			->expects($this->at(1))
			->method('addMethodCall')
			->with('select', [$database]);

		$this->_subject->process($this->_mock_container);
	}

}
