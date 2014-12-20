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
    private $mock_container;

    public function setUp()
    {
        $this->mock_container = $this->getMock(ContainerBuilder::class);

        $this->subject = new RedisScriptCompilerPass();
    }

    public function testProcess()
    {
        $redis_scripts  = $this->getMock(Definition::class);
        $script_service = $this->getMock(Definition::class);

        $tagged_services = [
        $service_id = 'service_id' => []
        ];

        $this->mock_container
        ->expects($this->at(0))
        ->method('getDefinition')
        ->with('RedisScripts')
        ->will($this->returnValue($redis_scripts));

        $this->mock_container
        ->expects($this->at(1))
        ->method('findTaggedServiceIds')
        ->with(RedisScriptCompilerPass::TAG)
        ->will($this->returnValue($tagged_services));

        $this->mock_container
        ->expects($this->at(2))
        ->method('getDefinition')
        ->with($service_id)
        ->will($this->returnValue($script_service));

        $script_service
        ->expects($this->once())
        ->method('getClass')
        ->will($this->returnValue(TestScript::class));

        $redis_scripts
        ->expects($this->at(0))
        ->method('addMethodCall')
        ->with('registerScript', ['name1', sha1('script1'), 'script1']);

        $redis_scripts
        ->expects($this->at(1))
        ->method('addMethodCall')
        ->with('registerScript', ['name2', sha1('script2'), 'script2']);

        $this->subject->process($this->mock_container);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Class Tests\BrainExe\Core\DependencyInjection\CompilerPass\RedisCompilerPass\RedisCompilerPassTest dies not implements the interface 'RedisScriptInterface'
     */
    public function testProcessWithInvalidClass()
    {
        $redis_scripts  = $this->getMock(Definition::class);
        $script_service = $this->getMock(Definition::class);

        $tagged_services = [
        $service_id = 'service_id' => []
        ];

        $this->mock_container
        ->expects($this->at(0))
        ->method('getDefinition')
        ->with('RedisScripts')
        ->will($this->returnValue($redis_scripts));

        $this->mock_container
        ->expects($this->at(1))
        ->method('findTaggedServiceIds')
        ->with(RedisScriptCompilerPass::TAG)
        ->will($this->returnValue($tagged_services));

        $this->mock_container
        ->expects($this->at(2))
        ->method('getDefinition')
        ->with($service_id)
        ->will($this->returnValue($script_service));

        $script_service
        ->expects($this->once())
        ->method('getClass')
        ->will($this->returnValue(RedisCompilerPassTest::class));

        $redis_scripts
        ->expects($this->never())
        ->method('addMethodCall');

        $this->subject->process($this->mock_container);
    }
}
