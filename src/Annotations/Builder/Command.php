<?php

namespace BrainExe\Core\Annotations\Builder;

use BrainExe\Annotations\Loader\Annotation\ServiceDefinitionBuilder;
use BrainExe\Core\DependencyInjection\CompilerPass\ConsoleCompilerPass;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Definition;

class Command extends ServiceDefinitionBuilder
{
    /**
     * {@inheritdoc}
     */
    public function build(ReflectionClass $reflectionClass, $annotation)
    {
        /** @var Definition $definition */
        list($serviceId, $definition) = parent::build($reflectionClass, $annotation);

        $definition->setPublic(false);
        $definition->addTag(ConsoleCompilerPass::TAG);

        return [$serviceId, $definition];
    }
}
