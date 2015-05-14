<?php

namespace Tests\BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\DebugCompilerPass;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @covers BrainExe\Core\DependencyInjection\CompilerPass\DebugCompilerPass
 */
class DebugCompilerPassTest extends TestCase
{

    /**
     * @var DebugCompilerPass
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new DebugCompilerPass();

    }

    public function testProcessWithoutDebug()
    {
        /** @var ContainerBuilder|MockObject $container */
        $container = $this->getMock(ContainerBuilder::class);

        $container->expects($this->once())
            ->method('getParameter')
            ->with('debug')
            ->willReturn(false);

        $container->expects($this->never())
            ->method('getDefinitions');

        $this->subject->process($container);
    }

    public function testProcessWithDebug()
    {

        /** @var ContainerBuilder|MockObject $container */
        $container = $this->getMock(ContainerBuilder::class);

        $container->expects($this->once())
            ->method('getParameter')
            ->with('debug')
            ->willReturn(true);

        /** @var MockObject|Definition $service */
        $service = $this->getMock(Definition::class);

        $container->expects($this->once())
            ->method('getDefinitions')
            ->willReturn([$service]);

        $service->expects($this->once())
            ->method('setPublic')
            ->with(true);

        $this->subject->process($container);
    }

}
