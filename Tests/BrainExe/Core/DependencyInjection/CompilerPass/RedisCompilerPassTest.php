<?php

namespace Tests\BrainExe\Core\DependencyInjection\CompilerPass\RedisCompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\RedisCompilerPass;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @Covers BrainExe\Core\DependencyInjection\CompilerPass\RedisCompilerPass
 */
class RedisCompilerPassTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var RedisCompilerPass
     */
    private $subject;

    /**
     * @var ContainerBuilder|MockObject
     */
    private $mockContainer;

    public function setUp()
    {
        $this->mockContainer = $this->getMock(ContainerBuilder::class);

        $this->subject = new RedisCompilerPass();
    }

    public function testProcess()
    {
        $this->markTestSkipped();
        
        $password = 'testetst';
        $database = 12;
        $host     = 'localhost';

        $redis = $this->getMock(Definition::class);

        $this->mockContainer
            ->expects($this->at(0))
            ->method('getDefinition')
            ->with('redis')
            ->willReturn($redis);
        $this->mockContainer
            ->expects($this->at(1))
            ->method('getParameter')
            ->with('redis.password')
            ->willReturn($password);
        $this->mockContainer
            ->expects($this->at(2))
            ->method('getParameter')
            ->with('redis.database')
            ->willReturn($database);
        $this->mockContainer
            ->expects($this->at(3))
            ->method('getParameter')
            ->with('redis.host')
            ->willReturn($host);

        $redis
            ->expects($this->at(0))
            ->method('setArguments')
            ->with([$password]);

//        $redis
//            ->expects($this->at(0))
//            ->method('addMethodCall')
//            ->with('auth', [$password]);
//        $redis
//            ->expects($this->at(1))
//            ->method('addMethodCall')
//            ->with('select', [$database]);
//        $redis
//            ->expects($this->at(1))
//            ->method('addMethodCall')
//            ->with('connect', ['host' => $host]);

        $this->subject->process($this->mockContainer);
    }
}
