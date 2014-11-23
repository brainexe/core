<?php

namespace BrainExe\Tests\Core\DependencyInjection;

use BrainExe\Core\DependencyInjection\ObjectFinder;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Component\DependencyInjection\Container;

class ObjectFinderTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var ObjectFinder
	 */
	private $_subject;

	/**
	 * @var Container|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockContainer;

	public function setup() {
		$this->_mockContainer = $this->getMock(Container::class);

		$this->_subject = new ObjectFinder($this->_mockContainer);
	}

	public function testGetService() {
		$service_id = 'FooService';
		$service = new \stdClass();

		$this->_mockContainer
			->expects($this->once())
			->method('get')
			->with($service_id)
			->will($this->returnValue($service));

		$actual = $this->_subject->getService($service_id);

		$this->assertEquals($service, $actual);
	}
} 
