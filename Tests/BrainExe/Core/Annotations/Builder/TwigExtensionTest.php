<?php

namespace Tests\BrainExe\Core\Annotations\Builder;

use BrainExe\Core\Annotations\Builder\TwigExtension as Builder;
use BrainExe\Core\Annotations\TwigExtension as Annotation;
use BrainExe\Core\DependencyInjection\CompilerPass\TwigExtensionCompilerPass;
use Doctrine\Common\Annotations\Reader;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Definition;

class TwigExtensionTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Builder
     */
    private $subject;

    /**
     * @var MockObject|Reader
     */
    private $reader;

    public function __construct()
    {
        $this->reader = $this->getMock(Reader::class);

        $this->subject = new Builder($this->reader);
    }

    public function testBuild()
    {
        $annotation = new Annotation([]);
        $annotation->compiler = $compiler = false;
        $annotation->name     = $name = 'name';

        /** @var MockObject|ReflectionClass $reflection */
        $reflection = $this->getMock(ReflectionClass::class, [], [], '', false);

        $reflection
            ->expects($this->any())
            ->method('getProperties')
            ->willReturn([]);
        $reflection
            ->expects($this->any())
            ->method('getMethods')
            ->willReturn([]);

        $actualResult = $this->subject->build($reflection, $annotation);

        $definition = new Definition();
        $definition->setPublic(false);
        $definition->addTag(TwigExtensionCompilerPass::TAG, ['compiler' => $compiler]);

        $expectedResult = [
            $name,
            $definition
        ];

        $this->assertEquals($expectedResult, $actualResult);

    }
}
