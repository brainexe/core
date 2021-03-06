<?php

namespace Tests\BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Application\AppKernel;
use BrainExe\Core\DependencyInjection\CompilerPass\MiddlewareCompilerPass;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @covers \BrainExe\Core\DependencyInjection\CompilerPass\MiddlewareCompilerPass
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
        $this->container  = $this->createMock(ContainerBuilder::class);
        $this->subject = new MiddlewareCompilerPass();
    }

    public function testProcess()
    {
        $appKernel = $this->createMock(Definition::class);

        $this->container
            ->expects($this->once())
            ->method('getParameter')
            ->with('application.middlewares')
            ->willReturn([
                $serviceId1 = 'service_id1',
            ]);

        $this->container
            ->expects($this->once())
            ->method('findDefinition')
            ->with(AppKernel::class)
            ->willReturn($appKernel);

        $appKernel
            ->expects($this->once())
            ->method('addArgument')
            ->with([new Reference($serviceId1)]);

        $this->container
            ->expects($this->once())
            ->method('getParameterBag')
            ->willReturn(new ParameterBag());

        $this->subject->process($this->container);
    }
}
