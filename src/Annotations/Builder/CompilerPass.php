<?php

namespace BrainExe\Core\Annotations\Builder;

use BrainExe\Core\Annotations\Service;
use BrainExe\Core\Annotations\Builder\ServiceDefinition;
use BrainExe\Core\Annotations\CompilerPass as CompilerPassAnnotation;
use Exception;
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
     *
     * @throws Exception
     */
    public function build(ReflectionClass $reflectionClass, Service $annotation, Definition $definition)
    {
        /** @var Definition $definition */
        [$serviceId, $definition] = parent::build($reflectionClass, $annotation, $definition);

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
