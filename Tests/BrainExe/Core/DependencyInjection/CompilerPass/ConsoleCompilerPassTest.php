<?php

namespace BrainExe\Tests\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\ConsoleCompilerPass;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ConsoleCompilerPassTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var ConsoleCompilerPass
     */
    private $subject;

    /**
     * @var ContainerBuilder|MockObject $container
     */
    private $mockContainer;

    /**
     * @var Definition|MockObject $container
     */
    private $mockConsoleDefinition;

    public function setUp()
    {
        $this->subject = new ConsoleCompilerPass();
        $this->mockContainer = $this->getMock(ContainerBuilder::class);
        $this->mockConsoleDefinition = $this->getMock(Definition::class);
    }

    public function testAddSubscriber()
    {
        $serviceId = 'FooListener';

        $this->mockContainer
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with(ConsoleCompilerPass::TAG)
            ->willReturn([$serviceId => []]);

        $this->mockContainer
            ->expects($this->once())
            ->method('getDefinition')
            ->with('Console')
            ->willReturn($this->mockConsoleDefinition);

        $this->mockConsoleDefinition
            ->expects($this->at(0))
            ->method('addMethodCall')
            ->with('setAutoExit', [false]);

        $this->mockConsoleDefinition
            ->expects($this->at(1))
            ->method('addMethodCall')
            ->with('add', [new Reference($serviceId)]);

        $this->subject->process($this->mockContainer);
    }
}
