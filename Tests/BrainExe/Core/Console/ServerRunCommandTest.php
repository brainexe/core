<?php

namespace Tests\BrainExe\Core\Console;

use BrainExe\Core\Console\ServerRunCommand;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @covers \BrainExe\Core\Console\ServerRunCommand
 */
class ServerRunCommandTest extends TestCase
{

    /**
     * @var ServerRunCommand
     */
    private $subject;

    /**
     * @var ProcessBuilder|MockObject
     */
    private $mockProcessBuilder;

    public function setUp()
    {
        $this->mockProcessBuilder = $this->createMock(ProcessBuilder::class);

        $this->subject = new ServerRunCommand($this->mockProcessBuilder, 'localhost:8080');
    }

    public function testExecute()
    {
        /** @var Application|MockObject $application */
        $application = $this->getMockBuilder(Application::class)
            ->setMethods(['run'])
            ->getMock();
        $this->subject->setApplication($application);

        $commandTester = new CommandTester($this->subject);

        $process = $this->createMock(Process::class);

        $this->mockProcessBuilder
            ->expects($this->once())
            ->method('setArguments')
            ->with([PHP_BINARY, '-S', 'localhost:8080'])
            ->willReturn($this->mockProcessBuilder);

        $this->mockProcessBuilder
            ->expects($this->once())
            ->method('setWorkingDirectory')
            ->with(ROOT . 'web/')
            ->willReturn($this->mockProcessBuilder);

        $this->mockProcessBuilder
            ->expects($this->once())
            ->method('setTimeout')
            ->with(null)
            ->willReturn($this->mockProcessBuilder);

        $this->mockProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($process);

        $process
            ->expects($this->once())
            ->method('run');

        $commandTester->execute([]);
        $output = $commandTester->getDisplay();

        $this->assertEquals("Server running on localhost:8080\n\n", $output);
    }
}
