<?php

namespace BrainExe\Tests\Core\DependencyInjection;

use BrainExe\Core\DependencyInjection\ObjectFinder;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\DependencyInjection\Container;

class ObjectFinderTest extends TestCase
{

    /**
     * @var ObjectFinder
     */
    private $subject;

    /**
     * @var Container|MockObject
     */
    private $container;

    public function setup()
    {
        $this->container = $this->getMock(Container::class);

        $this->subject = new ObjectFinder($this->container);
    }

    public function testGetService()
    {
        $serviceId = 'FooService';
        $service = new \stdClass();

        $this->container
            ->expects($this->once())
            ->method('get')
            ->with($serviceId)
            ->willReturn($service);

        $actual = $this->subject->getService($serviceId);

        $this->assertEquals($service, $actual);
    }
}
