<?php

namespace BrainExe\Core\Annotations\Builder;

use BrainExe\Annotations\Builder\ServiceDefinition;
use BrainExe\Core\DependencyInjection\CompilerPass\MiddlewareCompilerPass;
use Doctrine\Common\Annotations\Annotation;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Definition;

class Middleware extends ServiceDefinition
{
    /**
     * @param ReflectionClass $reflectionClass
     * @param Annotation $annotation
     * @return array
     */
    public function build(ReflectionClass $reflectionClass, Annotation $annotation)
    {
        /** @var Definition $definition */
        list($serviceId, $definition) = parent::build($reflectionClass, $annotation);

        $definition->addTag(MiddlewareCompilerPass::TAG);
        $definition->setPublic(false);

        return [$serviceId, $definition];
    }
}
