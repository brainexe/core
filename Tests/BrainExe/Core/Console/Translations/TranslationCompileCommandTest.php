<?php

namespace Tests\BrainExe\Core\Console\Translations\TranslationCompileCommand;

use BrainExe\Core\Console\Translations\TranslationCompileCommand;
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
 * @Covers BrainExe\Core\Console\Translations\TranslationCompileCommand
 */
class TranslationCompileCommandTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var TranslationCompileCommand
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

        $this->subject = new TranslationCompileCommand($this->mockFinder, $this->mockProcessBuilder, $this->mockFilesystem);
    }

    public function testExecuteWithoutExistingDir()
    {
        $application = new Application();
        $application->add($this->subject);

        $commandTester = new CommandTester($this->subject);

        $this->mockFilesystem
        ->expects($this->once())
        ->method('exists')
        ->with(ROOT . 'lang/')
        ->willReturn(false);

        $commandTester->execute([]);
        $output = $commandTester->getDisplay();

        $expectedResult = sprintf("Compile PO files...Lang directory does not exist: %slang/\ndone\n", ROOT);
        $this->assertEquals($expectedResult, $output);
    }

    public function testExecuteWithExistingDir()
    {
        $application = new Application();

        $application->add($this->subject);

        $commandTester = new CommandTester($this->subject);

        $this->mockFilesystem
        ->expects($this->once())
        ->method('exists')
        ->with(ROOT . 'lang/')
        ->willReturn(true);

        $file = new MockSplFileInfo([
        'relativePathname' => 'de_DE'
        ]);
        $files = [
        $file
        ];

        $process = $this->getMock(Process::class, [], [], '', false);

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

        $command = sprintf('msgfmt %slang/de_DE/LC_MESSAGES/messages.po -o %slang/de_DE/LC_MESSAGES/messages.mo', ROOT, ROOT);
        $this->mockProcessBuilder
        ->expects($this->once())
        ->method('setArguments')
        ->with([$command])
        ->willReturn($this->mockProcessBuilder);
        $this->mockProcessBuilder
        ->expects($this->once())
        ->method('getProcess')
        ->willReturn($process);

        $process
        ->expects($this->once())
        ->method('run');

        $process
        ->expects($this->once())
        ->method('isSuccessful')
        ->willReturn(true);

        $commandTester->execute([], ['verbosity' => OutputInterface::VERBOSITY_VERBOSE]);
        $output = $commandTester->getDisplay();

        $expectedResult = "Compile PO files...\nCompiled de_DE\ndone";
        $this->assertStringStartsWith($expectedResult, $output);
    }
}
