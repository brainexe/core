<?php

namespace Tests\BrainExe\Core\Console\ServerRunCommand;

use BrainExe\Core\Console\ServerRunCommand;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @Covers BrainExe\Core\Console\ServerRunCommand
 */
class ServerRunCommandTest extends PHPUnit_Framework_TestCase
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
        $this->mockProcessBuilder = $this->getMock(ProcessBuilder::class, [], [], '', false);

        $this->subject = new ServerRunCommand($this->mockProcessBuilder, 'localhost:8080');
    }

    public function testExecute()
    {
        /** @var Application|MockObject $application */
        $application = $this->getMock(Application::class, ['run']);
        $this->subject->setApplication($application);

        $commandTester = new CommandTester($this->subject);

        $process = $this->getMock(Process::class, [], [], '', false);

        $this->mockProcessBuilder
        ->expects($this->once())
        ->method('setArguments')
        ->with([PHP_BINARY, '-S', 'localhost:8080'])
        ->will($this->returnValue($this->mockProcessBuilder));

        $this->mockProcessBuilder
        ->expects($this->once())
        ->method('setWorkingDirectory')
        ->with(ROOT . 'web/')
        ->will($this->returnValue($this->mockProcessBuilder));

        $this->mockProcessBuilder
        ->expects($this->once())
        ->method('setTimeout')
        ->with(null)
        ->will($this->returnValue($this->mockProcessBuilder));

        $this->mockProcessBuilder
        ->expects($this->once())
        ->method('getProcess')
        ->will($this->returnValue($process));

        $process
        ->expects($this->once())
        ->method('run');

        $commandTester->execute([]);
        $output = $commandTester->getDisplay();

        $this->assertEquals("Server running on localhost:8080\n\n", $output);
    }
}
