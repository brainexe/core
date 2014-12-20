<?php

namespace BrainExe\Tests\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\GlobalCompilerPass;
use Monolog\Logger;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class GlobalCompilerPassTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var GlobalCompilerPass
     */
    private $subject;

    /**
     * @var ContainerBuilder|MockObject $container
     */
    private $mock_container;

    public function setUp()
    {
        $this->subject = new GlobalCompilerPass();
        $this->mock_container = $this->getMock(ContainerBuilder::class);
    }

    public function testProcessCompiler()
    {
        $service_id = 'FooCompilerPass';

        $compiler_mock = $this->getMock(CompilerPassInterface::class);
        $logger_mock = $this->getMock(Logger::class, [], [], '', false);

        $this->mock_container
        ->expects($this->at(0))
        ->method('setParameter');

        $this->mock_container
        ->expects($this->at(1))
        ->method('setParameter');

        $this->mock_container
        ->expects($this->at(2))
        ->method('findTaggedServiceIds')
        ->with(GlobalCompilerPass::TAG)
        ->will($this->returnValue([$service_id => [['priority' => $priority = 10]]]));

        $this->mock_container
        ->expects($this->at(3))
        ->method('get')
        ->with($service_id)
        ->will($this->returnValue($compiler_mock));

        $this->mock_container
        ->expects($this->at(4))
        ->method('get')
        ->with('monolog.logger')
        ->will($this->returnValue($logger_mock));

        $compiler_mock
        ->expects($this->once())
        ->method('process')
        ->with($this->mock_container);

        $this->subject->process($this->mock_container);
    }
}
