<?php

namespace BrainExe\Tests\Core\Traits;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Traits\EventDispatcherTrait;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

class TestEvent extends AbstractEvent
{
}

class EventDispatcherTraitTest extends TestCase
{

    /**
     * @var EventDispatcherTrait
     */
    private $subject;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = $this->getMockForTrait(EventDispatcherTrait::class);
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testDispatchEvent()
    {
        $event = new TestEvent('test');

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $this->subject->dispatchEvent($event);
    }
    public function testDispatchEventInBackground()
    {
        $event = new TestEvent('test');

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event);

        $this->subject->dispatchInBackground($event);
    }
}
