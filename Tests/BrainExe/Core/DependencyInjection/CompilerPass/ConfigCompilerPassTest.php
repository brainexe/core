<?php

namespace BrainExe\Tests\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\ConfigCompilerPass;
use BrainExe\Core\Util\FileSystem;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\ParameterBag;

class ConfigCompilerPassTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var ConfigCompilerPass
     */
    private $subject;

    /**
     * @var ContainerBuilder|MockObject $container
     */
    private $mockContainer;

    /**
     * @var ParameterBag|MockObject
     */
    private $mockParameterBag;

    /**
     * @var Finder|MockObject
     */
    private $mockFinder;

    /**
     * @var FileSystem|MockObject
     */
    private $mockFileSystem;

    public function setUp()
    {
        $this->mockContainer = $this->getMock(ContainerBuilder::class);
        $this->mockParameterBag = $this->getMock(ParameterBag::class);
        $this->mockFileSystem = $this->getMock(FileSystem::class);
        $this->mockFinder = $this->getMock(Finder::class, [], [], '', false);

        $this->subject = new ConfigCompilerPass();
    }

    public function testProcessWithInvalidRoot()
    {
        $this->markTestIncomplete();
        $this->mockContainer
        ->expects($this->once())
        ->method('setParameter')
        ->with('core_standalone');

        $this->mockFinder
        ->expects($this->once())
        ->method('files')
        ->willReturnSelf();
        $this->mockFinder
        ->expects($this->once())
        ->method('depth')
        ->willReturnSelf();
        $this->mockFinder
        ->expects($this->once())
        ->method('in')
        ->willReturnSelf();
        $this->mockFinder
        ->expects($this->once())
        ->method('name')
        ->willReturnSelf();

        $this->mockFileSystem
        ->expects($this->once())
        ->method('exists')
        ->with(ROOT . 'app')
        ->willReturn(false);

        $this->subject->process($this->mockContainer);
    }
}
