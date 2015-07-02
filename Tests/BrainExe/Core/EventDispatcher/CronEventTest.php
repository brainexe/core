<?php

namespace Tests\BrainExe\Core\EventDispatcher;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\CronEvent;
use PHPUnit_Framework_TestCase as TestCase;

class CronEventTest extends TestCase
{
    public function testConstructor()
    {
        /** @var AbstractEvent $wrapped */

        $wrapped = $this->getMock(AbstractEvent::class, [], [], '', false);
        $cron    = 3600;

        $event = new CronEvent($wrapped, $cron);

        $this->assertEquals($wrapped, $event->event);
        $this->assertEquals($cron, $event->expression);
    }
}
