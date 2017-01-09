<?php

namespace BrainExe\Core\Annotations\Builder;

use BrainExe\Annotations\Builder\ServiceDefinition;
use BrainExe\Core\DependencyInjection\CompilerPass\ConsoleCompilerPass;
use Doctrine\Common\Annotations\Annotation;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Definition;

class Command extends ServiceDefinition
{
    /**
     * {@inheritdoc}
     */
    public function build(ReflectionClass $reflectionClass, Annotation $annotation)
    {
        /** @var Definition $definition */
        list($serviceId, $definition) = parent::build($reflectionClass, $annotation);

        $definition->addTag(ConsoleCompilerPass::TAG);
        $definition->setPublic(true);
        $definition->setShared(false);
        $serviceId = sprintf('__console.%s', $serviceId);

        return [$serviceId, $definition];
    }
}
