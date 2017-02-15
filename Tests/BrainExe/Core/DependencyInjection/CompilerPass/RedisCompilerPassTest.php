<?php

namespace Tests\BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\RedisCompilerPass;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;

/**
 * @covers \BrainExe\Core\DependencyInjection\CompilerPass\RedisCompilerPass
 */
class RedisCompilerPassTest extends TestCase
{

    /**
     * @var RedisCompilerPass
     */
    private $subject;

    /**
     * @var ContainerBuilder|MockObject
     */
    private $container;

    public function setUp()
    {
        $this->container  = $this->createMock(ContainerBuilder::class);
        $this->subject = new RedisCompilerPass();
    }

    public function testProcess()
    {
        $uri = 'redis://localhost';

        $redis = $this->createMock(Definition::class);

        $this->container
            ->expects($this->at(0))
            ->method('findDefinition')
            ->with('Redis')
            ->willReturn($redis);
        $this->container
            ->expects($this->at(1))
            ->method('getParameter')
            ->with('redis.connection')
            ->willReturn($uri);
        $this->container
            ->expects($this->at(2))
            ->method('getParameter')
            ->with('redis.slave.connection')
            ->willThrowException(new ParameterNotFoundException('foo'));

        $redis
            ->expects($this->at(0))
            ->method('setArguments')
            ->with([
                0 => $uri
            ]);

        $this->subject->process($this->container);
    }
}
