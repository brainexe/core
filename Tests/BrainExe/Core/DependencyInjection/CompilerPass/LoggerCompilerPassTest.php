<?php

namespace BrainExe\Tests\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\LoggerCompilerPass;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class LoggerCompilerPassTest extends TestCase
{

    /**
     * @var LoggerCompilerPass
     */
    private $subject;

    /**
     * @var ContainerBuilder|MockObject $container
     */
    private $container;

    /**
     * @var Definition|MockObject $container
     */
    private $logger;

    public function setUp()
    {
        $this->subject = new LoggerCompilerPass();
        $this->container  = $this->getMock(ContainerBuilder::class, [
            'getDefinition',
            'getParameter',
        ]);
        $this->logger = $this->getMock(Definition::class);
    }

    public function testProcessCompiler()
    {
        $this->container
            ->expects($this->once())
            ->method('getDefinition')
            ->with('logger')
            ->willReturn($this->logger);

        $this->container
            ->expects($this->once())
            ->method('getParameter')
            ->with('logger.channels')
            ->willReturn([]);

        $this->subject->process($this->container);
    }
}
