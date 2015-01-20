<?php

namespace BrainExe\Tests\Core\Traits;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Traits\ConfigTrait;
use BrainExe\Core\Traits\EventDispatcherTrait;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\Container;

class TestEvent extends AbstractEvent
{
}

class EventDispatcherTraitTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var EventDispatcherTrait
     */
    private $subject;

    /**
     * @var EventDispatcher|MockObject
     */
    private $mockDispatcher;

    public function setUp()
    {
        $this->mockDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = $this->getMockForTrait(EventDispatcherTrait::class);
        $this->subject->setEventDispatcher($this->mockDispatcher);
    }

    public function testDispatchEvent()
    {
        $event = new TestEvent('test');

        $this->mockDispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $this->subject->dispatchEvent($event);
    }
    public function testDispatchEventInBackground()
    {
        $event = new TestEvent('test');

        $this->mockDispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event);

        $this->subject->dispatchInBackground($event);
    }
}
