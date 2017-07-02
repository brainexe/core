<?php

namespace BrainExe\Tests\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\EventListenerCompilerPass;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
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
    public function fooMethod() {}
    public function fooMethod2() {}
    public function fooMethod3() {}
}

class EventListenerCompilerPassTest extends TestCase
{

    /**
     * @var EventListenerCompilerPass
     */
    private $subject;

    /**
     * @var ContainerBuilder|MockObject $container
     */
    private $container;

    /**
     * @var Definition|MockObject $container
     */
    private $dispatcher;

    public function setUp()
    {
        $this->subject = new EventListenerCompilerPass();

        $this->container  = $this->createMock(ContainerBuilder::class);
        $this->dispatcher = $this->createMock(Definition::class);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Invalid event dispatcher method: FooService::fooMethod4()
     */
    public function testAddSubscriber()
    {
        $serviceId = 'FooService';

        $this->container
            ->expects($this->at(0))
            ->method('findDefinition')
            ->with('EventDispatcher')
            ->willReturn($this->dispatcher);

        $this->container
            ->expects($this->at(1))
            ->method('findTaggedServiceIds')
            ->with(EventListenerCompilerPass::TAG)
            ->willReturn([$serviceId => []]);

        $definition = $this->createMock(Definition::class);
        $definition
            ->expects($this->any())
            ->method('getClass')
            ->willReturn(TestEventDispatcher::class);
        $this->container
            ->expects($this->at(2))
            ->method('findDefinition')
            ->with($serviceId)
            ->willReturn($definition);
        $this->container
            ->expects($this->at(3))
            ->method('findDefinition')
            ->with($serviceId)
            ->willReturn($definition);
        $this->container
            ->expects($this->at(4))
            ->method('findDefinition')
            ->with($serviceId)
            ->willReturn($definition);
        $this->container
            ->expects($this->at(5))
            ->method('findDefinition')
            ->with($serviceId)
            ->willReturn($definition);

        $this->container
            ->expects($this->at(6))
            ->method('findDefinition')
            ->with($serviceId)
            ->willReturn($definition);

        $this->dispatcher
            ->expects($this->at(0))
            ->method('addMethodCall')
            ->with('addListener', [
                'foo_event',
                [new ServiceClosureArgument(new Reference($serviceId)), 'fooMethod'],
                0
            ]);

        $this->dispatcher
            ->expects($this->at(1))
            ->method('addMethodCall')
            ->with('addListener', [
                'foo_event2',
                [new ServiceClosureArgument(new Reference($serviceId)), 'fooMethod2'],
                10
            ]);

        $this->dispatcher
            ->expects($this->at(2))
            ->method('addMethodCall')
            ->with('addListener', [
                'foo_event3',
                [new ServiceClosureArgument(new Reference($serviceId)), 'fooMethod3'],
                0
            ]);

        $this->subject->process($this->container);
    }
}
