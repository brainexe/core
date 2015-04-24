<?php

namespace Tests\BrainExe\Core\Console\ClearCacheCommand;

use BrainExe\Core\Console\ClearCacheCommand;
use BrainExe\Core\DependencyInjection\Rebuild;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Util\FileSystem;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Finder\Finder;

/**
 * @Covers BrainExe\Core\Console\ClearCacheCommand
 */
class ClearCacheCommandTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var ClearCacheCommand
     */
    private $subject;

    /**
     * @var Finder|MockObject
     */
    private $mockFinder;

    /**
     * @var FileSystem|MockObject
     */
    private $mockFilesystem;

    /**
     * @var Rebuild|MockObject
     */
    private $mockRebuild;

    /**
     * @var EventDispatcher|MockObject
     */
    private $mockEventDispatcher;

    public function setUp()
    {
        $this->mockFinder = $this->getMock(Finder::class, [], [], '', false);
        $this->mockFilesystem = $this->getMock(FileSystem::class, [], [], '', false);
        $this->mockRebuild = $this->getMock(Rebuild::class, [], [], '', false);
        $this->mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new ClearCacheCommand($this->mockFinder, $this->mockFilesystem, $this->mockRebuild);
        $this->subject->setEventDispatcher($this->mockEventDispatcher);
    }

    public function testExecute()
    {
        /** @var Application|MockObject $application */
        $application = $this->getMock(Application::class, ['run']);
        $this->subject->setApplication($application);

        $commandTester = new CommandTester($this->subject);

        $files = [];

        $this->mockFinder
            ->expects($this->once())
            ->method('files')
            ->willReturn($this->mockFinder);

        $this->mockFinder
            ->expects($this->once())
            ->method('in')
            ->with(ROOT . 'cache')
            ->willReturn($this->mockFinder);

        $this->mockFinder
            ->expects($this->once())
            ->method('name')
            ->with('*.php')
            ->willReturn($this->mockFinder);

        $this->mockFinder
            ->expects($this->once())
            ->method('notname')
            ->with('assets.php')
            ->willReturn($files);

        $this->mockFilesystem
            ->expects($this->once())
            ->method('remove')
            ->with($files);

        $this->mockRebuild
            ->expects($this->once())
            ->method('rebuildDIC')
            ->with(true);

        $input = new ArrayInput(['command' => 'redis:scripts:load']);
        $application
            ->expects($this->once())
            ->method('run')
            ->with($input);

        $commandTester->execute([]);
        $output = $commandTester->getDisplay();

        $this->assertEquals("Clear Cache...done
Rebuild DIC...done\n", $output);
    }
}
