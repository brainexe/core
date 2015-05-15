<?php

namespace Tests\BrainExe\Core\DependencyInjection\CompilerPass\RedisCompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\RedisCompilerPass;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @covers BrainExe\Core\DependencyInjection\CompilerPass\RedisCompilerPass
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
        $this->container  = $this->getMock(ContainerBuilder::class, [
            'getParameter',
            'getDefinition',
        ]);
        $this->subject = new RedisCompilerPass();
    }

    public function testProcess()
    {
        $password = 'testetst';
        $database = 12;
        $host     = 'localhost';

        $redis = $this->getMock(Definition::class);

        $this->container
            ->expects($this->at(0))
            ->method('getDefinition')
            ->with('redis')
            ->willReturn($redis);
        $this->container
            ->expects($this->at(1))
            ->method('getParameter')
            ->with('redis.password')
            ->willReturn($password);
        $this->container
            ->expects($this->at(2))
            ->method('getParameter')
            ->with('redis.database')
            ->willReturn($database);
        $this->container
            ->expects($this->at(3))
            ->method('getParameter')
            ->with('redis.host')
            ->willReturn($host);

        $redis
            ->expects($this->at(0))
            ->method('setArguments')
            ->with([
                0 => [
                    'password' => $password,
                    'host' => $host,
                    'database' => $database
                ]
            ]);

        $this->subject->process($this->container);
    }
}
