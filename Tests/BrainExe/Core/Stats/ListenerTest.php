<?php

namespace Tests\BrainExe\Core\Stats;

use BrainExe\Core\Stats\Event;
use BrainExe\Core\Stats\Listener;
use BrainExe\Core\Stats\MultiEvent;
use BrainExe\Core\Stats\Stats;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

class ListenerTest extends TestCase
{

    /**
     * @var Listener
     */
    private $subject;

    /**
     * @var Stats|MockObject
     */
    private $stats;

    public function setUp()
    {
        $this->stats   = $this->createMock(Stats::class);
        $this->subject = new Listener($this->stats);
    }

    public function testHandleIncreaseEvent()
    {
        $event = new Event(Event::INCREASE, 'key', 23);

        $this->stats
            ->expects($this->once())
            ->method('increase')
            ->with(['key' => 23]);

        $this->subject->handleIncreaseEvent($event);
    }

    public function testHandleSetEvent()
    {
        $event = new Event(Event::SET, 'key', 23);

        $this->stats
            ->expects($this->once())
            ->method('set')
            ->with(['key' => 23]);

        $this->subject->handleSetEvent($event);
    }

    public function testHandleMultiIncreaseEvent()
    {
        $event = new MultiEvent(MultiEvent::INCREASE, ['key' => 32]);

        $this->stats
            ->expects($this->once())
            ->method('increase')
            ->with(['key' => 32]);

        $this->subject->handleMultiIncreaseEvent($event);
    }

    public function testHandleMultiSetEvent()
    {
        $event = new MultiEvent(MultiEvent::SET, ['key' => 32]);

        $this->stats
            ->expects($this->once())
            ->method('set')
            ->with(['key' => 32]);

        $this->subject->handleMultiSetEvent($event);
    }
}
