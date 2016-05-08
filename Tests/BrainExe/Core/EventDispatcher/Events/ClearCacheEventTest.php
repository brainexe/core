<?php

namespace Tests\BrainExe\Core\EventDispatcher\Events;

use BrainExe\Core\EventDispatcher\Events\ClearCacheEvent;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Console\Output\OutputInterface;

class ClearCacheEventTest extends TestCase
{

    public function testConstructor()
    {
        /** @var OutputInterface $output */
        $output = $this->getMock(OutputInterface::class);

        $event = new ClearCacheEvent($output);

        $this->assertEquals($output, $event->getOutput());
    }
}
