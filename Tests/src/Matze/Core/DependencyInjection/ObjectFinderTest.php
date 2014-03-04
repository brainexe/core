<?php

namespace Matze\Tests\Core\DependencyInjection;

use Matze\Core\DependencyInjection\ObjectFinder;
use Symfony\Component\DependencyInjection\Dump\Container;

class ObjectFinderTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var ObjectFinder
	 */
	private $_subject;

	/**
	 * @var Container|\PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mock_container;

	public function setup() {
		$this->_mock_container = $this->getMock('Symfony\Component\DependencyInjection\Container');
		$this->_subject = new ObjectFinder($this->_mock_container);
	}

	public function testgetService() {
		$service_id = 'FooService';
		$service = new \stdClass();

		$this->_mock_container
			->expects($this->once())
			->method('get')
			->with($service_id)
			->will($this->returnValue($service));

		$actual = $this->_subject->getService($service_id);

		$this->assertEquals($service, $actual);
	}
} 
