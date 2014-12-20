<?php

namespace Tests\BrainExe\Core\DependencyInjection\CompilerPass\TestCompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\TestCompilerPass;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @Covers BrainExe\Core\DependencyInjection\CompilerPass\TestCompilerPass
 */
class TestCompilerPassTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var TestCompilerPass
     */
    private $subject;

    /**
     * @var ContainerBuilder|MockObject
     */
    private $mockContainer;

    public function setUp()
    {
        $this->mockContainer = $this->getMock(ContainerBuilder::class);

        $this->subject = new TestCompilerPass();
    }

    public function testProcessWithoutStandalone()
    {
        $this->mockContainer
        ->expects($this->once())
        ->method('getParameter')
        ->willReturn(false);

        $this->subject->process($this->mockContainer);
    }

    public function testProcess()
    {
        $definition_1 = $this->getMock(Definition::class);
        $definition_2 = $this->getMock(Definition::class);

        $this->mockContainer
        ->expects($this->once())
        ->method('getParameter')
        ->willReturn(true);

        $this->mockContainer
        ->expects($this->once())
        ->method('getDefinitions')
        ->will($this->returnValue([$definition_1, $definition_2]));

        $definition_1
        ->expects($this->once())
        ->method('setPublic')
        ->with(true);

        $definition_2
        ->expects($this->once())
        ->method('setPublic')
        ->with(true);

        $this->subject->process($this->mockContainer);
    }
}
