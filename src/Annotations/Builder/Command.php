<?php

namespace BrainExe\Core\Annotations\Builder;

use BrainExe\Annotations\Builder\ServiceDefinition;
use BrainExe\Core\DependencyInjection\CompilerPass\ConsoleCompilerPass;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Definition;

class Command extends ServiceDefinition
{
    /**
     * {@inheritdoc}
     */
    public function build(ReflectionClass $reflectionClass, $annotation)
    {
        /** @var Definition $definition */
        list($serviceId, $definition) = parent::build($reflectionClass, $annotation);

        $definition->addTag(ConsoleCompilerPass::TAG);

        $serviceId = sprintf('__console.%s', $serviceId);

        return [$serviceId, $definition];
    }
}
