<?php

namespace Tests\BrainExe\Core\Console\Translations;

use BrainExe\Core\Console\Translations\TranslationFindCommand;
use BrainExe\Core\Util\Filesystem;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\Tests\Iterator\MockSplFileInfo;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @Covers BrainExe\Core\Console\Translations\TranslationFindCommand
 */
class TranslationFindCommandTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var TranslationFindCommand
     */
    private $subject;

    /**
     * @var Finder|MockObject
     */
    private $mockFinder;

    /**
     * @var ProcessBuilder|MockObject
     */
    private $mockProcessBuilder;

    /**
     * @var Filesystem|MockObject
     */
    private $mockFilesystem;

    public function setUp()
    {
        $this->mockFinder = $this->getMock(Finder::class, [], [], '', false);
        $this->mockProcessBuilder = $this->getMock(ProcessBuilder::class, [], [], '', false);
        $this->mockFilesystem = $this->getMock(Filesystem::class, [], [], '', false);

        $this->subject = new TranslationFindCommand(
            $this->mockFinder,
            $this->mockProcessBuilder,
            $this->mockFilesystem
        );
    }

    public function testExecute()
    {
        $application = new Application();
        $application->add($this->subject);

        $commandTester = new CommandTester($this->subject);
        $file = new MockSplFileInfo([
        'relativePathname' => 'de_DE'
        ]);
        $files = [
        $file
        ];

        $process = $this->getMock(Process::class, [], [], '', false);
        $this->mockProcessBuilder
            ->expects($this->at(0))
            ->method('setArguments')
            ->willReturn($this->mockProcessBuilder);
        $this->mockProcessBuilder
            ->expects($this->at(1))
            ->method('getProcess')
            ->willReturn($process);
        $process
            ->expects($this->once())
            ->method('run');
        $process
            ->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(true);

        $process2 = $this->getMock(Process::class, [], [], '', false);
        $this->mockProcessBuilder
            ->expects($this->at(2))
            ->method('setArguments')
            ->willReturn($this->mockProcessBuilder);
        $this->mockProcessBuilder
            ->expects($this->at(3))
            ->method('getProcess')
            ->willReturn($process2);
        $process2
            ->expects($this->once())
            ->method('run');
        $process2
            ->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(true);

        $this->mockFinder
            ->expects($this->at(0))
            ->method('directories')
            ->willReturn($this->mockFinder);
        $this->mockFinder
            ->expects($this->at(1))
            ->method('in')
            ->with(ROOT . 'lang/')
            ->willReturn($this->mockFinder);
        $this->mockFinder
            ->expects($this->at(2))
            ->method('depth')
            ->with(0)
            ->willReturn($files);

        $commandTester->execute([], ['verbosity' => OutputInterface::VERBOSITY_VERBOSE]);
        $output = $commandTester->getDisplay();

        $expectedResult = "Finds all marked translation...\nProcess de_DE\ndone in";
        $this->assertStringStartsWith($expectedResult, $output);
    }

    public function testExecuteWihErrors()
    {
        $application = new Application();
        $application->add($this->subject);

        $commandTester = new CommandTester($this->subject);
        $file = new MockSplFileInfo([
        'relativePathname' => 'de_DE'
        ]);
        $files = [
        $file
        ];

        $process = $this->getMock(Process::class, [], [], '', false);
        $this->mockProcessBuilder
            ->expects($this->at(0))
            ->method('setArguments')
            ->willReturn($this->mockProcessBuilder);
        $this->mockProcessBuilder
            ->expects($this->at(1))
            ->method('getProcess')
            ->willReturn($process);
        $process
            ->expects($this->once())
            ->method('run');
        $process
            ->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(false);
        $process
            ->expects($this->once())
            ->method('getErrorOutput')
            ->willReturn('error');
        $process
            ->expects($this->once())
            ->method('getCommandLine')
            ->willReturn('command');

        $process2 = $this->getMock(Process::class, [], [], '', false);
        $this->mockProcessBuilder
            ->expects($this->at(2))
            ->method('setArguments')
            ->willReturn($this->mockProcessBuilder);
        $this->mockProcessBuilder
            ->expects($this->at(3))
            ->method('getProcess')
            ->willReturn($process2);
        $process2
            ->expects($this->once())
            ->method('run');
        $process2
            ->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(true);

        $this->mockFinder
            ->expects($this->at(0))
            ->method('directories')
            ->willReturn($this->mockFinder);
        $this->mockFinder
            ->expects($this->at(1))
            ->method('in')
            ->with(ROOT . 'lang/')
            ->willReturn($this->mockFinder);
        $this->mockFinder
            ->expects($this->at(2))
            ->method('depth')
            ->with(0)
            ->willReturn($files);

        $commandTester->execute([], ['verbosity' => OutputInterface::VERBOSITY_VERBOSE]);
        $output = $commandTester->getDisplay();

        $expectedResult = "Finds all marked translation...\nError in command: command\nerror\nProcess de_DE\ndone in";
        $this->assertStringStartsWith($expectedResult, $output);
    }
}
