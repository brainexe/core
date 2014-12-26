<?php

namespace BrainExe\Tests\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\TwigExtensionCompilerPass;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Twig_Extension_Debug;
use Twig_Loader_Array;

class TwigExtensionCompilerPassTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var TwigExtensionCompilerPass
     */
    private $subject;

    /**
     * @var ContainerBuilder|MockObject $container
     */
    private $mockContainer;

    /**
     * @var Definition|MockObject $container
     */
    private $mockTwig;

    /**
     * @var Definition|MockObject $container
     */
    private $mockTwigCompiler;

    public function setUp()
    {
        $this->subject = new TwigExtensionCompilerPass();

        $this->mockContainer = $this->getMock(ContainerBuilder::class);
        $this->mockTwig = $this->getMock(Definition::class);
        $this->mockTwigCompiler = $this->getMock(Definition::class);
    }

    public function testProcessCompiler()
    {
        $serviceId = 'FooExtension';

        $mockExtension = $this->getMock(Definition::class);

        $this->mockContainer
            ->expects($this->at(0))
            ->method('getDefinition')
            ->with('Twig')
            ->willReturn($this->mockTwig);

        $this->mockContainer
            ->expects($this->at(1))
            ->method('getDefinition')
            ->with('TwigCompiler')
            ->willReturn($this->mockTwigCompiler);

        $this->mockContainer
            ->expects($this->at(2))
            ->method('findTaggedServiceIds')
            ->with(TwigExtensionCompilerPass::TAG)
            ->willReturn([$serviceId => [['compiler' => 0]]]);

        $this->mockContainer
            ->expects($this->at(3))
            ->method('getParameter')
            ->with('debug')
            ->willReturn(true);

        $this->mockContainer
            ->expects($this->at(4))
            ->method('getDefinition')
            ->with($serviceId)
            ->willReturn($mockExtension);

        $mockExtension
            ->expects($this->once())
            ->method('setPublic')
            ->with(false);

        $this->mockTwig
            ->expects($this->at(0))
            ->method('setArguments')
            ->with([new Definition(Twig_Loader_Array::class, [[]])]);

        $this->mockTwig
            ->expects($this->at(1))
            ->method('addMethodCall')
            ->with('addExtension', [new Reference($serviceId)]);

        $this->mockTwig
            ->expects($this->at(2))
            ->method('addMethodCall')
            ->with('addExtension', [new Definition(Twig_Extension_Debug::class)]);

        $this->mockTwig
            ->expects($this->at(3))
            ->method('addMethodCall')
            ->with('enableStrictVariables');

        $this->subject->process($this->mockContainer);
    }
}
