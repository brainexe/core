<?php

namespace Tests\BrainExe\Core\Stats;

use BrainExe\Core\Stats\Event;
use BrainExe\Core\Stats\Listener;
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
        $this->stats   = $this->getMock(Stats::class, [], [], '', false);
        $this->subject = new Listener($this->stats);
    }

    public function testGetSubscribedEvents()
    {
        $actual = $this->subject->getSubscribedEvents();

        $this->assertInternalType('array', $actual);
    }

    public function testHandleIncreaseEvent()
    {
        $event = new Event(Event::INCREASE, 'key', 'value');

        $this->stats
            ->expects($this->once())
            ->method('increase')
            ->with('key', 'value');

        $this->subject->handleIncreaseEvent($event);
    }

    public function testHandleSetEvent()
    {
        $event = new Event(Event::SET, 'key', 'value');

        $this->stats
            ->expects($this->once())
            ->method('set')
            ->with('key', 'value');

        $this->subject->handleSetEvent($event);
    }

}
