<?php

namespace BrainExe\Core\Annotations\Builder;

use BrainExe\Annotations\Builder\ServiceDefinition;
use BrainExe\Core\Annotations\CompilerPass as Annotation;
use BrainExe\Core\DependencyInjection\CompilerPass\MiddlewareCompilerPass;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Definition;

class Middleware extends ServiceDefinition
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

        $definition->setPublic(false);
        $definition->addTag(MiddlewareCompilerPass::TAG, [
            'priority' => $annotation->priority
        ]);

        return [$serviceId, $definition];
    }
}
