<?php

namespace BrainExe\Tests\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\Route;
use BrainExe\Core\DependencyInjection\CompilerPass\ControllerCompilerPass;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ControllerCompilerPassTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var ControllerCompilerPass|MockObject
     */
    private $subject;

    /**
     * @var ContainerBuilder|MockObject $container
     */
    private $container;

    /**
     * @var Definition|MockObject $container
     */
    private $routerDefinition;

    public function setUp()
    {
        $this->subject = $this->getMock(ControllerCompilerPass::class, ['dumpMatcher']);

        $this->container        = $this->getMock(ContainerBuilder::class);
        $this->routerDefinition = $this->getMock(Definition::class);
    }

    public function testProcess()
    {
        $route1 = new Route([]);
        $route2 = new Route([]);

        $route1->setCsrf(true);

        $service = $this->getMock(Definition::class);
        $serviceIds = [
            $serviceId = 'service_id' => [
                [$route1],
                [$route2],
            ]
        ];

        $this->container
            ->expects($this->at(0))
            ->method('getDefinition')
            ->with('Core.RouteCollection')
            ->willReturn($this->routerDefinition);

        $this->container
            ->expects($this->at(1))
            ->method('findTaggedServiceIds')
            ->with(ControllerCompilerPass::ROUTE_TAG)
            ->willReturn($serviceIds);

        $this->container
            ->expects($this->at(2))
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
