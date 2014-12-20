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
        $service_id = 'FooService';
        $service = new \stdClass();

        $this->mockContainer
        ->expects($this->once())
        ->method('get')
        ->with($service_id)
        ->will($this->returnValue($service));

        $actual = $this->subject->getService($service_id);

        $this->assertEquals($service, $actual);
    }
}
