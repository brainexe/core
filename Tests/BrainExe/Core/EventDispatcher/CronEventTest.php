<?php

namespace Tests\BrainExe\Core\EventDispatcher;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\CronEvent;
use PHPUnit\Framework\TestCase;

class CronEventTest extends TestCase
{
    public function testConstructor()
    {
        /** @var AbstractEvent $wrapped */
        $wrapped = $this->createMock(AbstractEvent::class);
        $cron    = 3600;

        $event = new CronEvent($wrapped, $cron);

        $this->assertEquals($wrapped, $event->event);
        $this->assertEquals($wrapped, $event->getEvent());
        $this->assertEquals($cron, $event->getExpression());
    }
}
