<?php

namespace Tests\BrainExe\Core\Annotations;

use BrainExe\Core\Annotations\Builder\TwigExtensionDefinitionBuilder;
use BrainExe\Core\Annotations\TwigExtension;
use BrainExe\Core\DependencyInjection\CompilerPass\TwigExtensionCompilerPass;
use Doctrine\Common\Annotations\Reader;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Definition;

class TwigExtensionDefinitionBuilderTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var TwigExtensionDefinitionBuilder
     */
    private $subject;

    /**
     * @var MockObject|Reader
     */
    private $mockReader;

    public function __construct()
    {
        $this->mockReader = $this->getMock(Reader::class);

        $this->subject = new TwigExtensionDefinitionBuilder($this->mockReader);
    }

    public function testBuild()
    {
        $annotation = new TwigExtension([]);
        $annotation->compiler = $compiler = false;
        $annotation->name     = $name = 'name';

        /** @var MockObject|ReflectionClass $reflection_class */
        $reflection_class = $this->getMock(ReflectionClass::class, [], [], '', false);

        $reflection_class
            ->expects($this->any())
            ->method('getProperties')
            ->willReturn([]);
        $reflection_class
            ->expects($this->any())
            ->method('getMethods')
            ->willReturn([]);

        $actualResult = $this->subject->build($reflection_class, $annotation);

        $definition = new Definition();
        $definition->setPublic(false);
        $definition->addTag(TwigExtensionCompilerPass::TAG, ['compiler' => $compiler]);

        $expectedResult = [
            'id'         => $name,
            'definition' => $definition
        ];

        $this->assertEquals($expectedResult, $actualResult);

    }
}
