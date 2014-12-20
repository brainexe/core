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
    private $mockContainer;

    /**
     * @var Definition|MockObject $container
     */
    private $mockRouterDefinition;

    public function setUp()
    {
        $this->subject = $this->getMock(ControllerCompilerPass::class, ['dumpMatcher']);

        $this->mockContainer        = $this->getMock(ContainerBuilder::class);
        $this->mockRouterDefinition = $this->getMock(Definition::class);
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

        $this->mockContainer
            ->expects($this->at(0))
            ->method('getDefinition')
            ->with('Core.RouteCollection')
            ->willReturn($this->mockRouterDefinition);

        $this->mockContainer
            ->expects($this->at(1))
            ->method('findTaggedServiceIds')
            ->with(ControllerCompilerPass::ROUTE_TAG)
            ->willReturn($serviceIds);

        $this->mockContainer
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
            ->with($this->mockContainer);

        $this->subject->process($this->mockContainer);
    }
}
