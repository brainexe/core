<?php

namespace BrainExe\Tests\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\EventListenerCompilerPass;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TestEventDispatcher implements EventSubscriberInterface {
	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents() {
		return [
			'foo_event' => 'fooMethod',
			'foo_event2' => ['fooMethod2', 10],
			'foo_event3' => [['fooMethod3'], ['fooMethod4', 20]]
		];
	}
}

class EventListenerCompilerPassTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var EventListenerCompilerPass
	 */
	private $_subject;

	/**
	 * @var ContainerBuilder|PHPUnit_Framework_MockObject_MockObject $container
	 */
	private $_mockContainer;

	/**
	 * @var Definition|PHPUnit_Framework_MockObject_MockObject $container
	 */
	private $_mockEventDispatcher;

	public function setUp() {
		$this->_subject = new EventListenerCompilerPass();

		$this->_mockContainer       = $this->getMock(ContainerBuilder::class);
		$this->_mockEventDispatcher = $this->getMock(Definition::class);
	}

	public function testAddSubscriber() {
		$service_id = 'FooService';

		$foo_service_mock = new TestEventDispatcher();

		$this->_mockContainer
			->expects($this->at(0))
			->method('getDefinition')
			->with('EventDispatcher')
			->will($this->returnValue($this->_mockEventDispatcher));

		$this->_mockContainer
			->expects($this->at(1))
			->method('findTaggedServiceIds')
			->with(EventListenerCompilerPass::TAG)
			->will($this->returnValue([$service_id => []]));

		$definition = $this->getMock(Definition::class);
		$definition
			->expects($this->once())
			->method('getClass')
			->willReturn(TestEventDispatcher::class);
		$this->_mockContainer
			->expects($this->at(2))
			->method('getDefinition')
			->with($service_id)
			->will($this->returnValue($definition));

		$this->_mockEventDispatcher
			->expects($this->at(0))
			->method('addMethodCall')
			->with('addListenerService', ['foo_event', [$service_id, 'fooMethod'], 0]);

		$this->_mockEventDispatcher
			->expects($this->at(1))
			->method('addMethodCall')
			->with('addListenerService', ['foo_event2', [$service_id, 'fooMethod2'], 10]);

		$this->_mockEventDispatcher
			->expects($this->at(2))
			->method('addMethodCall')
			->with('addListenerService', ['foo_event3', [$service_id, 'fooMethod3'], 0]);

		$this->_mockEventDispatcher
			->expects($this->at(3))
			->method('addMethodCall')
			->with('addListenerService', ['foo_event3', [$service_id, 'fooMethod4'], 20]);

		$this->_subject->process($this->_mockContainer);
	}

}
