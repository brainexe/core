<?php

namespace Tests\BrainExe\Core\Console;

use BrainExe\Core\Console\ClearCacheCommand;
use BrainExe\Core\DependencyInjection\Rebuild;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \BrainExe\Core\Console\ClearCacheCommand
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

        $this->subject = new ClearCacheCommand(
            $this->rebuild,
            $this->dispatcher
        );
    }

    public function testExecute()
    {
        /** @var Application|MockObject $application */
        $application = $this->getMockBuilder(Application::class)
            ->setMethods(['run'])
            ->getMock();
        $this->subject->setApplication($application);

        $commandTester = new CommandTester($this->subject);

        $this->rebuild
            ->expects($this->once())
            ->method('buildContainer');

        $commandTester->execute([]);
        $output = $commandTester->getDisplay();

        $this->assertEquals("Rebuild DIC...done\n", $output);
    }
}
