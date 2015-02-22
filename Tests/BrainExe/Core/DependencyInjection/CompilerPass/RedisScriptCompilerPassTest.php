<?php

namespace Tests\BrainExe\Core\DependencyInjection\CompilerPass\RedisScriptCompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\RedisScriptCompilerPass;
use BrainExe\Core\Redis\RedisScriptInterface;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use SebastianBergmann\Exporter\Exception;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Tests\BrainExe\Core\DependencyInjection\CompilerPass\RedisCompilerPass\RedisCompilerPassTest;

class TestScript implements RedisScriptInterface
{

    /**
     * {@inheritdoc}
     */
    public static function getRedisScripts()
    {
        return [
            'name1' => 'script1',
            'name2' => 'script2',
        ];
    }
}

/**
 * @Covers BrainExe\Core\DependencyInjection\CompilerPass\RedisScriptCompilerPass
 */
class RedisScriptCompilerPassTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var RedisScriptCompilerPass
     */
    private $subject;

    /**
     * @var ContainerBuilder|MockObject
     */
    private $mockContainer;

    public function setUp()
    {
        $this->mockContainer = $this->getMock(ContainerBuilder::class);

        $this->subject = new RedisScriptCompilerPass();
    }

    public function testProcess()
    {
        $redisScripts  = $this->getMock(Definition::class);
        $scriptService = $this->getMock(Definition::class);

        $taggedServices = [
            $serviceId = 'service_id' => []
        ];

        $this->mockContainer
            ->expects($this->at(0))
            ->method('getDefinition')
            ->with('RedisScripts')
            ->willReturn($redisScripts);

        $this->mockContainer
            ->expects($this->at(1))
            ->method('findTaggedServiceIds')
            ->with(RedisScriptCompilerPass::TAG)
            ->willReturn($taggedServices);

        $this->mockContainer
            ->expects($this->at(2))
            ->method('getDefinition')
            ->with($serviceId)
            ->willReturn($scriptService);

        $scriptService
            ->expects($this->once())
            ->method('getClass')
            ->willReturn(TestScript::class);

        $redisScripts
            ->expects($this->at(0))
            ->method('addMethodCall')
            ->with('registerScript', ['name1', sha1('script1'), 'script1']);

        $redisScripts
            ->expects($this->at(1))
            ->method('addMethodCall')
            ->with('registerScript', ['name2', sha1('script2'), 'script2']);

        $this->subject->process($this->mockContainer);
    }

    /**
     * @expectedException Exception
     */
    public function testProcessWithInvalidClass()
    {
        $redisScripts  = $this->getMock(Definition::class);
        $scriptService = $this->getMock(Definition::class);

        $taggedServices = [
            $serviceId = 'service_id' => []
        ];

        $this->mockContainer
            ->expects($this->at(0))
            ->method('getDefinition')
            ->with('RedisScripts')
            ->willReturn($redisScripts);

        $this->mockContainer
            ->expects($this->at(1))
            ->method('findTaggedServiceIds')
            ->with(RedisScriptCompilerPass::TAG)
            ->willReturn($taggedServices);

        $this->mockContainer
            ->expects($this->at(2))
            ->method('getDefinition')
            ->with($serviceId)
            ->willReturn($scriptService);

        $scriptService
            ->expects($this->once())
            ->method('getClass')
            ->willReturn(RedisCompilerPassTest::class);

        $redisScripts
            ->expects($this->never())
            ->method('addMethodCall');

        $this->subject->process($this->mockContainer);
    }
}
