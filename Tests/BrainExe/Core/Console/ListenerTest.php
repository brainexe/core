<?php

namespace Tests\BrainExe\Core\Console;

use BrainExe\Core\Console\Listener;
use BrainExe\Core\EventDispatcher\Events\ConsoleEvent;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

class ListenerTest extends TestCase
{

    /**
     * @var Listener
     */
    private $subject;

    /**
     * @var Application|MockObject
     */
    private $application;

    public function setUp()
    {
        $this->application = $this->getMock(Application::class, [], [], '', false);

        $this->subject = new Listener($this->application);
    }

    public function testHandleEvent()
    {
        $command = 'myCommand';
        $output = new BufferedOutput();

        $event = new ConsoleEvent($command, $output);

        $this->application
            ->expects($this->once())
            ->method('setAutoExit')
            ->with(false);

        $this->application
            ->expects($this->once())
            ->method('run')
            ->with(new StringInput($command), $output);

        $this->subject->handleEvent($event);
    }
}
