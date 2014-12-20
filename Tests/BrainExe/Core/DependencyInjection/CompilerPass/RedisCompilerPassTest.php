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
    private $mock_container;

    public function setUp()
    {
        $this->mock_container = $this->getMock(ContainerBuilder::class);

        $this->subject = new RedisCompilerPass();
    }

    public function testProcess()
    {
        $password = 'testetst';
        $database = 12;

        $redis = $this->getMock(Definition::class);

        $this->mock_container
        ->expects($this->at(0))
        ->method('getDefinition')
        ->with('redis')
        ->will($this->returnValue($redis));

        $this->mock_container
        ->expects($this->at(1))
        ->method('getParameter')
        ->with('redis.password')
        ->will($this->returnValue($password));

        $this->mock_container
        ->expects($this->at(2))
        ->method('getParameter')
        ->with('redis.database')
        ->will($this->returnValue($database));

        $redis
        ->expects($this->at(0))
        ->method('addMethodCall')
        ->with('auth', [$password]);

        $redis
        ->expects($this->at(1))
        ->method('addMethodCall')
        ->with('select', [$database]);

        $this->subject->process($this->mock_container);
    }
}
