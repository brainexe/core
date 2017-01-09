<?php

namespace BrainExe\Core\Annotations\Builder;

use BrainExe\Annotations\Builder\ServiceDefinition;
use BrainExe\Core\Annotations\CompilerPass as CompilerPassAnnotation;
use BrainExe\Core\DependencyInjection\CompilerPass\GlobalCompilerPass;
use Doctrine\Common\Annotations\Annotation;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Definition;

class CompilerPass extends ServiceDefinition
{
    /**
     * @param ReflectionClass $reflectionClass
     * @param Annotation|CompilerPassAnnotation $annotation
     * @return array
     */
    public function build(ReflectionClass $reflectionClass, Annotation $annotation)
    {
        /** @var Definition $definition */
        list($serviceId, $definition) = parent::build($reflectionClass, $annotation);

        $definition->addTag(GlobalCompilerPass::TAG, ['priority' => $annotation->priority]);
        $definition->setPublic(false);

        return [$serviceId, $definition];
    }
}
