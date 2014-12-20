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
    private $mock_container;

    /**
     * @var Definition|MockObject $container
     */
    private $mock_definition;

    public function setUp()
    {
        $this->subject = new SetDefinitionFileCompilerPass();
        $this->mock_container = $this->getMock(ContainerBuilder::class);
        $this->mock_definition = $this->getMock(Definition::class);
    }

    public function testProcessCompilerWithInvalidDefinition()
    {
        $service_id = 'FooService';

        $this->mock_container
        ->expects($this->once())
        ->method('getServiceIds')
        ->will($this->returnValue([$service_id]));

        $this->mock_container
        ->expects($this->once())
        ->method('hasDefinition')
        ->with($service_id)
        ->will($this->returnValue(false));

        $this->subject->process($this->mock_container);
    }

    public function testProcessCompiler()
    {
        $service_id = 'FooService';

        $this->mock_container
        ->expects($this->once())
        ->method('getServiceIds')
        ->will($this->returnValue([$service_id]));

        $this->mock_container
        ->expects($this->once())
        ->method('hasDefinition')
        ->with($service_id)
        ->will($this->returnValue(true));

        $this->mock_definition
        ->expects($this->once())
        ->method('getClass')
        ->will($this->returnValue(FooTestClass::class));

        $this->mock_definition
        ->expects($this->once())
        ->method('setFile')
        ->with(__FILE__);

        $this->mock_container
        ->expects($this->once())
        ->method('getDefinition')
        ->with($service_id)
        ->will($this->returnValue($this->mock_definition));

        $this->subject->process($this->mock_container);
    }

    public function testProcessCompilerWithInvalidFile()
    {
        $service_id = 'FooService';

        $this->mock_container
        ->expects($this->once())
        ->method('getServiceIds')
        ->will($this->returnValue([$service_id]));

        $this->mock_container
        ->expects($this->once())
        ->method('hasDefinition')
        ->with($service_id)
        ->will($this->returnValue(true));

        $this->mock_definition
        ->expects($this->once())
        ->method('getClass')
        ->will($this->returnValue('InvalidClass'));

        $this->mock_definition
        ->expects($this->never())
        ->method('setFile');

        $this->mock_container
        ->expects($this->once())
        ->method('getDefinition')
        ->with($service_id)
        ->will($this->returnValue($this->mock_definition));

        $this->subject->process($this->mock_container);
    }
}
