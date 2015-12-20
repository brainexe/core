<?php

namespace BrainExe\Tests\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\GlobalCompilerPass;
use Monolog\Logger;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class GlobalCompilerPassTest extends TestCase
{

    /**
     * @var GlobalCompilerPass
     */
    private $subject;

    /**
     * @var ContainerBuilder|MockObject $container
     */
    private $container;

    public function setUp()
    {
        $this->subject   = new GlobalCompilerPass();
        $this->container = $this->getMock(ContainerBuilder::class, [
            'getDefinition',
            'getParameter',
            'setParameter',
            'get',
            'findTaggedServiceIds',
            'reset'
        ]);
    }

    public function testProcessCompiler()
    {
        $serviceId = 'FooCompilerPass';
        $compiler  = $this->getMock(CompilerPassInterface::class);
        $logger    = $this->getMock(Logger::class, [], [], '', false);

        $this->container
            ->expects($this->at(0))
            ->method('setParameter');

        $this->container
            ->expects($this->at(1))
            ->method('findTaggedServiceIds')
            ->with(GlobalCompilerPass::TAG)
            ->willReturn([$serviceId => [['priority' => 10]]]);

        $this->container
            ->expects($this->at(2))
            ->method('get')
            ->with($serviceId)
            ->willReturn($compiler);

        $this->container
            ->expects($this->at(3))
            ->method('reset');

        $this->container
            ->expects($this->at(4))
            ->method('get')
            ->with('logger')
            ->willReturn($logger);
        $compiler
            ->expects($this->once())
            ->method('process')
            ->with($this->container);

        $this->subject->process($this->container);
    }
}
