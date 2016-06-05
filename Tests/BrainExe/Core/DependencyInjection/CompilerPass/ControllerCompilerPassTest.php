<?php

namespace BrainExe\Tests\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\Route;
use BrainExe\Core\DependencyInjection\CompilerPass\ControllerCompilerPass;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ControllerCompilerPassTest extends TestCase
{

    /**
     * @var ControllerCompilerPass|MockObject
     */
    private $subject;

    /**
     * @var ContainerBuilder|MockObject
     */
    private $container;

    /**
     * @var Definition|MockObject
     */
    private $routerDefinition;

    public function setUp()
    {
        $this->subject   = $this->getMockBuilder(ControllerCompilerPass::class)
            ->setMethods(['dumpMatcher'])
            ->getMock();
        $this->container = $this->createMock(ContainerBuilder::class);
        $this->routerDefinition = $this->createMock(Definition::class);
    }

    public function testProcess()
    {
        $route1 = new Route(['name' => 'foo']);
        $route2 = new Route(['name' => 'bar']);

        $route1->setCsrf(true);

        $service = $this->createMock(Definition::class);
        $serviceIds = [
            $serviceId = 'service_id' => [
                [$route1],
                [$route2],
            ]
        ];

        $this->container
            ->expects($this->at(0))
            ->method('findTaggedServiceIds')
            ->with(ControllerCompilerPass::ROUTE_TAG)
            ->willReturn($serviceIds);

        $this->container
            ->expects($this->at(1))
            ->method('getDefinition')
            ->with($serviceId)
            ->willReturn($service);

        $service
            ->expects($this->once())
            ->method('clearTag')
            ->with(ControllerCompilerPass::ROUTE_TAG);

        $this->subject
            ->expects($this->once())
            ->method('dumpMatcher')
            ->with($this->container);

        $this->subject->process($this->container);
    }
}
