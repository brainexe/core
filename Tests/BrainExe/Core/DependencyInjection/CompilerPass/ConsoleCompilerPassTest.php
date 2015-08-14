<?php

namespace BrainExe\Tests\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Console\ProxyCommand;
use BrainExe\Core\DependencyInjection\CompilerPass\ConsoleCompilerPass;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ConsoleCompilerPassTest extends TestCase
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
            'get'
        ]);
        $this->consoleDefinition = $this->getMock(Definition::class);
    }

    public function testAddSubscriber()
    {
        $serviceId = 'FooListener';

        $definition = new InputDefinition();

        $command = $this->getMock(Command::class, [], [], '', false);
        $command
            ->expects($this->once())
            ->method('getDefinition')
            ->willReturn($definition);
        $command
            ->expects($this->once())
            ->method('getName')
            ->willReturn('mockName');
        $command
            ->expects($this->once())
            ->method('getDescription')
            ->willReturn('mockDescription');
        $command
            ->expects($this->once())
            ->method('getAliases')
            ->willReturn('mockAliases');

        $this->container
            ->expects($this->at(0))
            ->method('getDefinition')
            ->with('Console')
            ->willReturn($this->consoleDefinition);

        $this->container
            ->expects($this->at(1))
            ->method('findTaggedServiceIds')
            ->with(ConsoleCompilerPass::TAG)
            ->willReturn([$serviceId => []]);

        $this->container
            ->expects($this->at(2))
            ->method('get')
            ->with($serviceId)
            ->willReturn($command);

        $this->consoleDefinition
            ->expects($this->at(0))
            ->method('addMethodCall')
            ->with('setAutoExit', [false]);

        $expectedDefinition = new Definition(ProxyCommand::class, [
            new Reference('service_container'),
            new Reference('console'),
            $serviceId,
            'mockName',
            'mockDescription',
            'mockAliases',
            []
        ]);

        $this->consoleDefinition
            ->expects($this->at(1))
            ->method('addMethodCall')
            ->with('addCommands', [[$expectedDefinition]]);

        $this->subject->process($this->container);
    }
}
