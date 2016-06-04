<?php

namespace Tests\BrainExe\Core\Console;

use BrainExe\Core\Console\ClearCacheCommand;
use BrainExe\Core\DependencyInjection\Rebuild;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers BrainExe\Core\Console\ClearCacheCommand
 */
class ClearCacheCommandTest extends TestCase
{

    /**
     * @var ClearCacheCommand
     */
    private $subject;

    /**
     * @var Rebuild|MockObject
     */
    private $rebuild;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->rebuild    = $this->createMock(Rebuild::class);
        $this->dispatcher = $this->createMock(EventDispatcher::class);

        $this->subject = new ClearCacheCommand($this->rebuild);
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testExecute()
    {
        /** @var Application|MockObject $application */
        $application = $this->getMock(Application::class, ['run']);
        $this->subject->setApplication($application);

        $commandTester = new CommandTester($this->subject);

        $this->rebuild
            ->expects($this->once())
            ->method('rebuildDIC')
            ->with(true);

        $commandTester->execute([]);
        $output = $commandTester->getDisplay();

        $this->assertEquals("Rebuild DIC...done\n", $output);
    }
}
