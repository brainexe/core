<?php

namespace BrainExe\Core\Annotations\Builder;

use BrainExe\Annotations\Builder\ServiceDefinition;
use BrainExe\Core\Annotations\CompilerPass as Annotation;
use BrainExe\Core\DependencyInjection\CompilerPass\GlobalCompilerPass;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Definition;

class CompilerPass extends ServiceDefinition
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

        $definition->addTag(GlobalCompilerPass::TAG, ['priority' => $annotation->priority]);
        $definition->setPublic(false);

        return [$serviceId, $definition];
    }
}
