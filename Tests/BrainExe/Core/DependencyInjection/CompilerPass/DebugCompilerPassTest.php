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

    /**
     * @var ContainerBuilder|MockObject
     */
    private $container;

    public function setUp()
    {
        $this->container  = $this->getMock(ContainerBuilder::class, [
            'getDefinitions',
            'getParameter',
        ]);

        $this->subject = new DebugCompilerPass();
    }

    public function testProcessWithoutDebug()
    {
        $this->container->expects($this->once())
            ->method('getParameter')
            ->with('debug')
            ->willReturn(false);

        $this->container->expects($this->never())
            ->method('getDefinitions');

        $this->subject->process($this->container);
    }

    public function testProcessWithDebug()
    {
        $this->container->expects($this->once())
            ->method('getParameter')
            ->with('debug')
            ->willReturn(true);

        /** @var MockObject|Definition $service */
        $service = $this->getMock(Definition::class);

        $this->container->expects($this->once())
            ->method('getDefinitions')
            ->willReturn([$service]);

        $service->expects($this->once())
            ->method('setPublic')
            ->with(true);

        $this->subject->process($this->container);
    }
}
