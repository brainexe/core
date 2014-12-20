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
    private $mock_container;

    /**
     * @var Definition|MockObject $container
     */
    private $mock_console_definition;

    public function setUp()
    {
        $this->subject = new ConsoleCompilerPass();
        $this->mock_container = $this->getMock(ContainerBuilder::class);
        $this->mock_console_definition = $this->getMock(Definition::class);
    }

    public function testAddSubscriber()
    {
        $service_id = 'FooListener';

        $this->mock_container
        ->expects($this->once())
        ->method('findTaggedServiceIds')
        ->with(ConsoleCompilerPass::TAG)
        ->will($this->returnValue([$service_id => []]));

        $this->mock_container
        ->expects($this->once())
        ->method('getDefinition')
        ->with('Console')
        ->will($this->returnValue($this->mock_console_definition));

        $this->mock_console_definition
        ->expects($this->at(0))
        ->method('addMethodCall')
        ->with('setAutoExit', [false]);

        $this->mock_console_definition
        ->expects($this->at(1))
        ->method('addMethodCall')
        ->with('add', [new Reference($service_id)]);

        $this->subject->process($this->mock_container);
    }
}
