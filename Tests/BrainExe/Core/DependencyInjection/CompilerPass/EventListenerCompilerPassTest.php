<?php

namespace BrainExe\Tests\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\EventListenerCompilerPass;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TestEventDispatcher implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
        'foo_event' => 'fooMethod',
        'foo_event2' => ['fooMethod2', 10],
        'foo_event3' => [['fooMethod3'], ['fooMethod4', 20]]
        ];
    }
}

class EventListenerCompilerPassTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var EventListenerCompilerPass
     */
    private $subject;

    /**
     * @var ContainerBuilder|MockObject $container
     */
    private $mockContainer;

    /**
     * @var Definition|MockObject $container
     */
    private $mockEventDispatcher;

    public function setUp()
    {
        $this->subject = new EventListenerCompilerPass();

        $this->mockContainer       = $this->getMock(ContainerBuilder::class);
        $this->mockEventDispatcher = $this->getMock(Definition::class);
    }

    public function testAddSubscriber()
    {
        $serviceId = 'FooService';

        $this->mockContainer
            ->expects($this->at(0))
            ->method('getDefinition')
            ->with('EventDispatcher')
            ->willReturn($this->mockEventDispatcher);

        $this->mockContainer
            ->expects($this->at(1))
            ->method('findTaggedServiceIds')
            ->with(EventListenerCompilerPass::TAG)
            ->willReturn([$serviceId => []]);

        $definition = $this->getMock(Definition::class);
        $definition
            ->expects($this->once())
            ->method('getClass')
            ->willReturn(TestEventDispatcher::class);
        $this->mockContainer
            ->expects($this->at(2))
            ->method('getDefinition')
            ->with($serviceId)
            ->willReturn($definition);

        $this->mockEventDispatcher
            ->expects($this->at(0))
            ->method('addMethodCall')
            ->with('addListenerService', ['foo_event', [$serviceId, 'fooMethod'], 0]);

        $this->mockEventDispatcher
            ->expects($this->at(1))
            ->method('addMethodCall')
            ->with('addListenerService', ['foo_event2', [$serviceId, 'fooMethod2'], 10]);

        $this->mockEventDispatcher
            ->expects($this->at(2))
            ->method('addMethodCall')
            ->with('addListenerService', ['foo_event3', [$serviceId, 'fooMethod3'], 0]);

        $this->mockEventDispatcher
            ->expects($this->at(3))
            ->method('addMethodCall')
            ->with('addListenerService', ['foo_event3', [$serviceId, 'fooMethod4'], 20]);

        $this->subject->process($this->mockContainer);
    }
}
