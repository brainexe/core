<?php

namespace Tests\BrainExe\Core\Application\SelfUpdate\SelfUpdateCommand;

use BrainExe\Core\Application\SelfUpdate\SelfUpdate;
use BrainExe\Core\Application\SelfUpdate\SelfUpdateCommand;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers BrainExe\Core\Application\SelfUpdate\SelfUpdateCommand
 */
class SelfUpdateCommandTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var SelfUpdateCommand
     */
    private $subject;

    /**
     * @var SelfUpdate|MockObject
     */
    private $mockSelfUpdate;

    /**
     * @var EventDispatcher|MockObject
     */
    private $mockEventDispatcher;

    public function setUp()
    {
        $this->mockSelfUpdate = $this->getMock(SelfUpdate::class, [], [], '', false);
        $this->mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new SelfUpdateCommand($this->mockSelfUpdate, $this->mockEventDispatcher);
    }

    public function testExecute()
    {
        $application = new Application();
        $application->add($this->subject);

        $commandTester = new CommandTester($this->subject);

        $this->mockSelfUpdate
            ->expects($this->once())
            ->method('startUpdate');

        $commandTester->execute([]);
    }
}
