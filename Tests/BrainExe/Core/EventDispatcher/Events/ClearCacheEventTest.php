<?php

namespace Tests\BrainExe\Core\EventDispatcher\Events;

use BrainExe\Core\EventDispatcher\Events\ClearCacheEvent;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearCacheEventTest extends TestCase
{

    public function testConstructor()
    {
        /** @var Application $application */
        $application = $this->getMock(Application::class);
        /** @var InputInterface $input */
        $input       = $this->getMock(InputInterface::class);
        /** @var OutputInterface $output */
        $output      = $this->getMock(OutputInterface::class);

        $event = new ClearCacheEvent($application, $input, $output);

        $this->assertEquals($application, $event->application);
        $this->assertEquals($input, $event->input);
        $this->assertEquals($output, $event->output);
    }
}
