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
    private $container;

    /**
     * @var Definition|MockObject $container
     */
    private $consoleDefinition;

    public function setUp()
    {
        $this->subject = new ConsoleCompilerPass();
        $this->container  = $this->getMock(ContainerBuilder::class, [
            'findTaggedServiceIds',
            'getDefinition',
        ]);
        $this->consoleDefinition = $this->getMock(Definition::class);
    }

    public function testAddSubscriber()
    {
        $serviceId = 'FooListener';

        $this->container
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with(ConsoleCompilerPass::TAG)
            ->willReturn([$serviceId => []]);

        $this->container
            ->expects($this->once())
            ->method('getDefinition')
            ->with('Console')
            ->willReturn($this->consoleDefinition);

        $this->consoleDefinition
            ->expects($this->at(0))
            ->method('addMethodCall')
            ->with('setAutoExit', [false]);

        $this->consoleDefinition
            ->expects($this->at(1))
            ->method('addMethodCall')
            ->with('addCommands', [[new Reference($serviceId)]]);

        $this->subject->process($this->container);
    }
}
