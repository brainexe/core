<?php

namespace BrainExe\Tests\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\SetDefinitionFileCompilerPass;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class FooTestClass
{

}

class SetDefinitionFileCompilerPassTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var SetDefinitionFileCompilerPass
     */
    private $subject;

    /**
     * @var ContainerBuilder|MockObject $container
     */
    private $mockContainer;

    /**
     * @var Definition|MockObject $container
     */
    private $mockDefinition;

    public function setUp()
    {
        $this->subject = new SetDefinitionFileCompilerPass();
        $this->mockContainer = $this->getMock(ContainerBuilder::class);
        $this->mockDefinition = $this->getMock(Definition::class);
    }

    public function testProcessCompilerWithInvalidDefinition()
    {
        $serviceId = 'FooService';

        $this->mockContainer
            ->expects($this->once())
            ->method('getServiceIds')
            ->willReturn([$serviceId]);

        $this->mockContainer
            ->expects($this->once())
            ->method('hasDefinition')
            ->with($serviceId)
            ->willReturn(false);

        $this->subject->process($this->mockContainer);
    }

    public function testProcessCompiler()
    {
        $serviceId = 'FooService';

        $this->mockContainer
            ->expects($this->once())
            ->method('getServiceIds')
            ->willReturn([$serviceId]);

        $this->mockContainer
            ->expects($this->once())
            ->method('hasDefinition')
            ->with($serviceId)
            ->willReturn(true);

        $this->mockDefinition
            ->expects($this->once())
            ->method('getClass')
            ->willReturn(FooTestClass::class);

        $this->mockDefinition
            ->expects($this->once())
            ->method('setFile')
            ->with(__FILE__);

        $this->mockContainer
            ->expects($this->once())
            ->method('getDefinition')
            ->with($serviceId)
            ->willReturn($this->mockDefinition);

        $this->subject->process($this->mockContainer);
    }

    public function testProcessCompilerWithInvalidFile()
    {
        $serviceId = 'FooService';

        $this->mockContainer
            ->expects($this->once())
            ->method('getServiceIds')
            ->willReturn([$serviceId]);

        $this->mockContainer
            ->expects($this->once())
            ->method('hasDefinition')
            ->with($serviceId)
            ->willReturn(true);

        $this->mockDefinition
            ->expects($this->once())
            ->method('getClass')
            ->willReturn('InvalidClass');

        $this->mockDefinition
            ->expects($this->never())
            ->method('setFile');

        $this->mockContainer
            ->expects($this->once())
            ->method('getDefinition')
            ->with($serviceId)
            ->willReturn($this->mockDefinition);

        $this->subject->process($this->mockContainer);
    }
}
