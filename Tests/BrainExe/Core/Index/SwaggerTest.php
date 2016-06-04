<?php

namespace BrainExe\Tests\Core\Index;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\EventDispatcher\Events\ConsoleEvent;
use BrainExe\Core\Index\Swagger;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers BrainExe\Core\Index\Swagger
 */
class SwaggerTest extends TestCase
{

    /**
     * @var Swagger
     */
    private $subject;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->dispatcher = $this->createMock(EventDispatcher::class);

        $this->subject = new Swagger();
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testSwagger()
    {
        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($this->isInstanceOf(ConsoleEvent::class));

        $actual = $this->subject->dump();

        $this->assertEquals(200, $actual->getStatusCode());
        $this->assertEquals('text/x-yaml', $actual->headers->get('Content-Type'));
    }
}
