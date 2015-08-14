<?php

namespace BrainExe\Tests\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\ConfigCompilerPass;
use BrainExe\Core\Util\FileSystem;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\ParameterBag;

class ConfigCompilerPassTest extends TestCase
{

    /**
     * @var ConfigCompilerPass
     */
    private $subject;

    /**
     * @var ContainerBuilder|MockObject $container
     */
    private $container;

    /**
     * @var ParameterBag|MockObject
     */
    private $parameterBag;

    /**
     * @var Finder|MockObject
     */
    private $finder;

    /**
     * @var FileSystem|MockObject
     */
    private $fileSystem;

    public function setUp()
    {
        $this->container  = $this->getMock(ContainerBuilder::class, [
            'setParameter',
        ]);
        $this->parameterBag = $this->getMock(ParameterBag::class);
        $this->fileSystem   = $this->getMock(FileSystem::class);
        $this->finder       = $this->getMock(Finder::class, [], [], '', false);

        $this->subject = new ConfigCompilerPass($this->finder);
    }

    public function testProcessWithInvalidRoot()
    {
        $this->markTestIncomplete();
        $this->container
            ->expects($this->once())
            ->method('setParameter')
            ->with('core_standalone');

        $this->finder
            ->expects($this->once())
            ->method('files')
            ->willReturnSelf();
        $this->finder
            ->expects($this->once())
            ->method('depth')
            ->willReturnSelf();
        $this->finder
            ->expects($this->once())
            ->method('in')
            ->willReturnSelf();
        $this->finder
            ->expects($this->once())
            ->method('name')
            ->willReturn([]);

        $this->fileSystem
            ->expects($this->once())
            ->method('exists')
            ->with(ROOT . 'app')
            ->willReturn(false);

        $this->subject->process($this->container);
    }
}
