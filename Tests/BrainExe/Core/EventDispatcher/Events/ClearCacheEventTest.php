<?php

namespace Tests\BrainExe\Core\EventDispatcher\Events;

use BrainExe\Core\EventDispatcher\Events\ClearCacheEvent;
use PHPUnit\Framework\TestCase;

class ClearCacheEventTest extends TestCase
{

    public function testConstructor()
    {
        $event = new ClearCacheEvent();

        $this->assertEquals(ClearCacheEvent::NAME, $event->getEventName());
    }
}
