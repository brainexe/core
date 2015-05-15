<?php

namespace BrainExe\Tests\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\LoggerCompilerPass;
use Monolog\Handler\ChromePHPHandler;
use Monolog\Handler\HipChatHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
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
            'findTaggedServiceIds'
        ]);
        $this->logger    = $this->getMock(Definition::class);
    }

    public function testProcessCompilerWithCoreStandaloneAndHipchat()
    {
        $this->container
            ->expects($this->at(0))
            ->method('getDefinition')
            ->with('monolog.Logger')
            ->willReturn($this->logger);

        $this->container
            ->expects($this->at(1))
            ->method('getParameter')
            ->with('core_standalone')
            ->willReturn(true);

        $this->container
            ->expects($this->at(2))
            ->method('getParameter')
            ->with('debug')
            ->willReturn(false);

        $this->container
            ->expects($this->at(3))
            ->method('getParameter')
            ->with('hipchat.api_token')
            ->willReturn('hipchat_token');
        $this->container
            ->expects($this->at(4))
            ->method('getParameter')
            ->with('hipchat.api_token')
            ->willReturn('hipchat_token');
        $this->container
            ->expects($this->at(5))
            ->method('getParameter')
            ->with('hipchat.room')
            ->willReturn('hipchat_room');
        $this->container
            ->expects($this->at(6))
            ->method('getParameter')
            ->with('hipchat.name')
            ->willReturn('hipchat_name');
        $this->container
            ->expects($this->at(7))
            ->method('getParameter')
            ->with('hipchat.logLevel')
            ->willReturn('hipchat_loglevel');

        $this->logger
            ->expects($this->at(3))
            ->method('addMethodCall')
            ->with('pushHandler', [new Definition(HipChatHandler::class, [
                'hipchat_token', 'hipchat_room', 'hipchat_name', false, 'hipchat_loglevel'
            ])]);

        $this->subject->process($this->container);
    }

    public function testProcessCompilerWitDebug()
    {
        $this->container
            ->expects($this->at(0))
            ->method('getDefinition')
            ->with('monolog.Logger')
            ->willReturn($this->logger);

        $this->container
            ->expects($this->at(1))
            ->method('getParameter')
            ->with('core_standalone')
            ->willReturn(false);

        $this->container
            ->expects($this->at(2))
            ->method('getParameter')
            ->with('debug')
            ->willReturn(true);

        $this->logger
            ->expects($this->at(0))
            ->method('addMethodCall')
            ->with('pushHandler', [new Definition(ChromePHPHandler::class)]);

        $this->logger
            ->expects($this->at(1))
            ->method('addMethodCall')
            ->with('pushHandler', [new Definition(StreamHandler::class, ['php://stdout', Logger::INFO])]);

        $this->subject->process($this->container);
    }
}
