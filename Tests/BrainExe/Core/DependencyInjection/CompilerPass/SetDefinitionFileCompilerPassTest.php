<?php

namespace BrainExe\Tests\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\SetDefinitionFileCompilerPass;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class FooTestClass
{

}

class SetDefinitionFileCompilerPassTest extends TestCase
{

    /**
     * @var SetDefinitionFileCompilerPass
     */
    private $subject;

    /**
     * @var ContainerBuilder|MockObject $container
     */
    private $container;

    /**
     * @var Definition|MockObject $container
     */
    private $definition;

    public function setUp()
    {
        $this->subject    = new SetDefinitionFileCompilerPass();
        $this->container  = $this->getMock(ContainerBuilder::class, [
            'getServiceIds',
            'getDefinition',
            'hasDefinition'
        ]);
        $this->definition = $this->getMock(Definition::class);
    }

    public function testProcessCompilerWithInvalidDefinition()
    {
        $serviceId = 'FooService';

        $this->container
            ->expects($this->once())
            ->method('getServiceIds')
            ->willReturn([$serviceId]);

        $this->container
            ->expects($this->once())
            ->method('hasDefinition')
            ->with($serviceId)
            ->willReturn(false);

        $this->subject->process($this->container);
    }

    public function testProcessCompiler()
    {
        $serviceId = 'FooService';

        $this->container
            ->expects($this->once())
            ->method('getServiceIds')
            ->willReturn([$serviceId]);

        $this->container
            ->expects($this->once())
            ->method('hasDefinition')
            ->with($serviceId)
            ->willReturn(true);

        $this->definition
            ->expects($this->once())
            ->method('getClass')
            ->willReturn(FooTestClass::class);

        $this->definition
            ->expects($this->once())
            ->method('setFile')
            ->with(__FILE__);

        $this->container
            ->expects($this->once())
            ->method('getDefinition')
            ->with($serviceId)
            ->willReturn($this->definition);

        $this->subject->process($this->container);
    }

    public function testProcessCompilerWithInvalidFile()
    {
        $serviceId = 'FooService';

        $this->container
            ->expects($this->once())
            ->method('getServiceIds')
            ->willReturn([$serviceId]);

        $this->container
            ->expects($this->once())
            ->method('hasDefinition')
            ->with($serviceId)
            ->willReturn(true);

        $this->definition
            ->expects($this->once())
            ->method('getClass')
            ->willReturn('InvalidClass');

        $this->definition
            ->expects($this->never())
            ->method('setFile');

        $this->container
            ->expects($this->once())
            ->method('getDefinition')
            ->with($serviceId)
            ->willReturn($this->definition);

        $this->subject->process($this->container);
    }
}
