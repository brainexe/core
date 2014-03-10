<?php

namespace Matze\Tests\Core\DependencyInjection\CompilerPass;

use Matze\Core\DependencyInjection\CompilerPass\EventListenerCompilerPass;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
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
	private $_mock_container;

	/**
	 * @var Definition|PHPUnit_Framework_MockObject_MockObject $container
	 */
	private $_mock_event_dispatcher_definition;

	public function setUp() {
		$this->_subject = new EventListenerCompilerPass();
		$this->_mock_container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
		$this->_mock_event_dispatcher_definition = $this->getMock('Symfony\Component\DependencyInjection\Definition');
	}

	public function testAddSubscriber() {
		$service_id = 'FooService';

		$foo_service_mock = new TestEventDispatcher();

		$this->_mock_container
			->expects($this->at(0))
			->method('findTaggedServiceIds')
			->with(EventListenerCompilerPass::TAG)
			->will($this->returnValue([$service_id => []]));

		$this->_mock_container
			->expects($this->at(1))
			->method('getDefinition')
			->with('EventDispatcher')
			->will($this->returnValue($this->_mock_event_dispatcher_definition));

		$mock_definition = $this->getMock('Symfony\Component\DependencyInjection\Definition');

		$this->_mock_container
			->expects($this->at(2))
			->method('getDefinition')
			->with($service_id)
			->will($this->returnValue($mock_definition));

		$mock_definition
			->expects($this->once())
			->method('setPublic')
			->with(false);

		$this->_mock_container
			->expects($this->at(3))
			->method('get')
			->with($service_id)
			->will($this->returnValue($foo_service_mock));

		$this->_mock_event_dispatcher_definition
			->expects($this->at(0))
			->method('addMethodCall')
			->with('addListener', ['foo_event', [new Reference($service_id), 'fooMethod'], 0]);

		$this->_mock_event_dispatcher_definition
			->expects($this->at(1))
			->method('addMethodCall')
			->with('addListener', ['foo_event2', [new Reference($service_id), 'fooMethod2'], 10]);

		$this->_mock_event_dispatcher_definition
			->expects($this->at(2))
			->method('addMethodCall')
			->with('addListener', ['foo_event3', [new Reference($service_id), 'fooMethod3'], 0]);

		$this->_mock_event_dispatcher_definition
			->expects($this->at(3))
			->method('addMethodCall')
			->with('addListener', ['foo_event3', [new Reference($service_id), 'fooMethod4'], 20]);

		$this->_subject->process($this->_mock_container);
	}

} 
