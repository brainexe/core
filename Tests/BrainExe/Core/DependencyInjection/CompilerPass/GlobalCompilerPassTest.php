<?php

namespace BrainExe\Tests\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\GlobalCompilerPass;
use Monolog\Logger;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class GlobalCompilerPassTest extends \PHPUnit_Framework_TestCase
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
        $this->subject    = new GlobalCompilerPass();
        $this->container  = $this->getMock(ContainerBuilder::class, [
            'getDefinition',
            'getParameter',
            'setParameter',
            'get',
            'findTaggedServiceIds'
        ]);
    }

    public function testProcessCompiler()
    {
        $serviceId = 'FooCompilerPass';

        $compilerMock = $this->getMock(CompilerPassInterface::class);
        $loggerMock = $this->getMock(Logger::class, [], [], '', false);

        $this->container
            ->expects($this->at(0))
            ->method('setParameter');

        $this->container
            ->expects($this->at(1))
            ->method('setParameter');

        $this->container
            ->expects($this->at(2))
            ->method('findTaggedServiceIds')
            ->with(GlobalCompilerPass::TAG)
            ->willReturn([$serviceId => [['priority' => 10]]]);

        $this->container
            ->expects($this->at(3))
            ->method('get')
            ->with($serviceId)
            ->willReturn($compilerMock);

        $this->container
            ->expects($this->at(4))
            ->method('get')
            ->with('monolog.logger')
            ->willReturn($loggerMock);

        $compilerMock
            ->expects($this->once())
            ->method('process')
            ->with($this->container);

        $this->subject->process($this->container);
    }
}
