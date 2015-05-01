<?php

namespace BrainExe\Core\Annotations\Builder;

use BrainExe\Annotations\Loader\Annotation\ServiceDefinitionBuilder;
use BrainExe\Core\Annotations\TwigExtension as Annotation;
use BrainExe\Core\DependencyInjection\CompilerPass\TwigExtensionCompilerPass;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Definition;

class TwigExtension extends ServiceDefinitionBuilder
{

    /**
     * @param ReflectionClass $reflectionClass
     * @param Annotation $annotation
     * @return array
     */
    public function build(ReflectionClass $reflectionClass, $annotation)
    {
        /** @var Definition $definition */
        list($serviceId, $definition) = parent::build($reflectionClass, $annotation);

        $definition->addTag(TwigExtensionCompilerPass::TAG, ['compiler' => $annotation->compiler]);
        $definition->setPublic(false);

        return [$serviceId, $definition];
    }
}
