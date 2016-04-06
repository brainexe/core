<?php

namespace Tests\BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\MiddlewareCompilerPass;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @covers BrainExe\Core\DependencyInjection\CompilerPass\MiddlewareCompilerPass
 */
class MiddlewareCompilerPassTest extends TestCase
{

    /**
     * @var MiddlewareCompilerPass
     */
    private $subject;

    /**
     * @var ContainerBuilder|MockObject
     */
    private $container;

    public function setUp()
    {
        $this->container  = $this->getMock(ContainerBuilder::class, [
            'getDefinition',
            'setParameter',
            'getParameter',
            'addArgument'
        ]);
        $this->subject = new MiddlewareCompilerPass();
    }

    public function testProcess()
    {
        $appKernel = $this->getMock(Definition::class);

        $this->container
            ->expects($this->once())
            ->method('getParameter')
            ->with('application.middlewares')
            ->willReturn([
                $serviceId1 = 'service_id1',
            ]);

        $this->container
            ->expects($this->once())
            ->method('getDefinition')
            ->with('AppKernel')
            ->willReturn($appKernel);

        $appKernel
            ->expects($this->once())
            ->method('replaceArgument')
            ->with(3, [new Reference($serviceId1)]);

        $this->container
            ->expects($this->once())
            ->method('setParameter')
            ->with('application.middlewares', []);

        $this->subject->process($this->container);
    }
}
