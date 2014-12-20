<?php

namespace Tests\BrainExe\Core\DependencyInjection\CompilerPass\MiddlewareCompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\MiddlewareCompilerPass;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @Covers BrainExe\Core\DependencyInjection\CompilerPass\MiddlewareCompilerPass
 */
class MiddlewareCompilerPassTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var MiddlewareCompilerPass
     */
    private $subject;

    /**
     * @var ContainerBuilder|MockObject
     */
    private $mockContainer;

    public function setUp()
    {
        $this->mockContainer = $this->getMock(ContainerBuilder::class);

        $this->subject = new MiddlewareCompilerPass();

    }

    public function testProcess()
    {
        $app_kernel = $this->getMock(Definition::class);

        $service_ids = [
        $service_id_1 = 'service_id1' => [0 => ['priority' => 5]],
        $service_id_2 = 'service_id2' => [0 => ['priority' => null]],
        ];

        $this->mockContainer
        ->expects($this->once())
        ->method('findTaggedServiceIds')
        ->with(MiddlewareCompilerPass::TAG)
        ->will($this->returnValue($service_ids));

        $this->mockContainer
        ->expects($this->once())
        ->method('getDefinition')
        ->with('AppKernel')
        ->will($this->returnValue($app_kernel));

        $app_kernel
        ->expects($this->once())
        ->method('addMethodCall')
        ->with('setMiddlewares', [[new Reference($service_id_1)]]);

        $this->subject->process($this->mockContainer);
    }
}
