<?php

namespace BrainExe\Tests\Core\DependencyInjection;

use BrainExe\Core\DependencyInjection\ObjectFinder;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\DependencyInjection\Container;

class ObjectFinderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ObjectFinder
     */
    private $subject;

    /**
     * @var Container|MockObject
     */
    private $mockContainer;

    public function setup()
    {
        $this->mockContainer = $this->getMock(Container::class);

        $this->subject = new ObjectFinder($this->mockContainer);
    }

    public function testGetService()
    {
        $serviceId = 'FooService';
        $service = new \stdClass();

        $this->mockContainer
        ->expects($this->once())
        ->method('get')
        ->with($serviceId)
        ->willReturn($service);

        $actual = $this->subject->getService($serviceId);

        $this->assertEquals($service, $actual);
    }
}
