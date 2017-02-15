<?php

namespace BrainExe\Core\Annotations\Builder;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Annotations\Builder\ServiceDefinition;
use BrainExe\Core\Annotations\CompilerPass as CompilerPassAnnotation;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;

class CompilerPass extends ServiceDefinition
{

    /**
     * @param ReflectionClass $reflectionClass
     * @param Service|CompilerPassAnnotation $annotation
     * @param Definition $definition
     * @return array
     */
    public function build(ReflectionClass $reflectionClass, Service $annotation, Definition $definition)
    {
        /** @var Definition $definition */
        list($serviceId, $definition) = parent::build($reflectionClass, $annotation, $definition);

        $definition->setPublic(false);
        $this->container->setDefinition($serviceId, $definition);

        /** @var CompilerPassInterface $compilerPass */
        $compilerPass = $this->container->get($serviceId);
        $this->container->addCompilerPass(
            $compilerPass,
            $annotation->type,
            $annotation->priority
        );

        return [$serviceId, $definition];
    }
}
