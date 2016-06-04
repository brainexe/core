<?php

namespace BrainExe\Tests\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\MergeDefinitions;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class MergeDefinitionsTest extends TestCase
{

    /**
     * @var MergeDefinitions
     */
    private $subject;

    /**
     * @var ContainerBuilder|MockObject $container
     */
    private $container;

    public function setUp()
    {
        $this->subject = new MergeDefinitions();
        $this->container = $this->createMock(ContainerBuilder::class);
    }

    public function testProcessCompiler()
    {
        $this->container
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with(MergeDefinitions::TAG)
            ->willReturn([
                'serviceId' => [
                    ['parent' => 'parentId']
                ]
            ]);

        $child  = $this->createMock(Definition::class);
        $parent = $this->createMock(Definition::class);

        $this->container
            ->expects($this->at(1))
            ->method('getDefinition')
            ->with('serviceId')
            ->willReturn($child);

        $this->container
            ->expects($this->at(2))
            ->method('getDefinition')
            ->with('parentId')
            ->willReturn($parent);

        $child
            ->expects($this->once())
            ->method('getMethodCalls')
            ->willReturn([
               ['method1', ['param1']],
               ['method2', ['param2']],
            ]);

        $parent
            ->expects($this->at(0))
            ->method('addMethodCall')
            ->with('method1', ['param1']);

        $parent
            ->expects($this->at(1))
            ->method('addMethodCall')
            ->with('method2', ['param2']);

        $this->subject->process($this->container);
    }
}
