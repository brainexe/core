<?php

namespace BrainExe\Tests\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\LoggerCompilerPass;
use Monolog\Handler\ChromePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class LoggerCompilerPassTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var LoggerCompilerPass
     */
    private $subject;

    /**
     * @var ContainerBuilder|MockObject $container
     */
    private $mock_container;

    /**
     * @var Definition|MockObject $container
     */
    private $mockLoggerDefinition;

    public function setUp()
    {
        $this->subject = new LoggerCompilerPass();

        $this->mock_container = $this->getMock(ContainerBuilder::class);
        $this->mockLoggerDefinition = $this->getMock(Definition::class);
    }

    public function testProcessCompilerWithCoreStandalone()
    {
        $this->mock_container
        ->expects($this->once())
        ->method('getParameter')
        ->with('core_standalone')
        ->will($this->returnValue(true));

        $this->mock_container
        ->expects($this->once())
        ->method('getDefinition')
        ->with('monolog.Logger')
        ->will($this->returnValue($this->mockLoggerDefinition));

        $this->subject->process($this->mock_container);
    }

    public function testProcessCompilerWitDebug()
    {
        $this->mock_container
        ->expects($this->at(0))
        ->method('getDefinition')
        ->with('monolog.Logger')
        ->will($this->returnValue($this->mockLoggerDefinition));

        $this->mock_container
        ->expects($this->at(1))
        ->method('getParameter')
        ->with('core_standalone')
        ->will($this->returnValue(false));

        $this->mock_container
        ->expects($this->at(2))
        ->method('getParameter')
        ->with('debug')
        ->will($this->returnValue(true));

        $this->mockLoggerDefinition
        ->expects($this->at(0))
        ->method('addMethodCall')
        ->with('pushHandler', [new Definition(ChromePHPHandler::class)]);

        $this->mockLoggerDefinition
        ->expects($this->at(1))
        ->method('addMethodCall')
        ->with('pushHandler', [new Definition(StreamHandler::class, ['php://stdout', Logger::INFO])]);

        $this->subject->process($this->mock_container);
    }
}
