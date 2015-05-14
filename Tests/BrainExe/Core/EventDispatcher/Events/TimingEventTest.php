<?php

namespace Tests\BrainExe\Core\EventDispatcher\Events;

use BrainExe\Core\EventDispatcher\Events\TimingEvent;
use PHPUnit_Framework_TestCase as TestCase;

class TimingEventTest extends TestCase
{

    public function testConstructor()
    {
        $timingId = 'timing';

        $event = new TimingEvent($timingId);

        $this->assertEquals($timingId, $event->timingId);
    }
}
