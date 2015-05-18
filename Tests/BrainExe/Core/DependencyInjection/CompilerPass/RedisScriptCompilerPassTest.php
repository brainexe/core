<?php

namespace Tests\BrainExe\Core\DependencyInjection\CompilerPass\RedisScriptCompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\RedisScriptCompilerPass;
use BrainExe\Core\Redis\RedisScript;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @covers BrainExe\Core\DependencyInjection\CompilerPass\RedisScriptCompilerPass
 */
class RedisScriptCompilerPassTest extends TestCase
{

    /**
     * @var RedisScriptCompilerPass
     */
    private $subject;

    /**
     * @var ContainerBuilder|MockObject
     */
    private $container;

    public function setUp()
    {
        $this->container  = $this->getMock(ContainerBuilder::class, [
            'getServiceIds',
            'getDefinition',
            'hasDefinition',
            'get',
            'findTaggedServiceIds'
        ]);
        $this->subject = new RedisScriptCompilerPass();
    }

    public function testProcess()
    {
        $redis         = $this->getMock(Definition::class);
        $scriptService = $this->getMock(Definition::class);
        $redisScript   = $this->getMock(RedisScript::class);

        $taggedServices = [
            $serviceId = 'service_id' => []
        ];

        $this->container
            ->expects($this->at(0))
            ->method('getDefinition')
            ->with('redis')
            ->willReturn($redis);

        $this->container
            ->expects($this->at(1))
            ->method('findTaggedServiceIds')
            ->with(RedisScriptCompilerPass::TAG)
            ->willReturn($taggedServices);

        $this->container
            ->expects($this->at(2))
            ->method('getDefinition')
            ->with($serviceId)
            ->willReturn($scriptService);

        $this->container
            ->expects($this->at(3))
            ->method('get')
            ->with($serviceId)
            ->willReturn($redisScript);

        $redisScript
            ->expects($this->once())
            ->method('getName')
            ->willReturn('name1');
        $scriptService
            ->expects($this->once())
            ->method('getClass')
            ->willReturn('classname');

        $redis
            ->expects($this->once())
            ->method('addMethodCall')
            ->with('defineCommand', ['name1', 'classname']);

        $this->subject->process($this->container);
    }
}
