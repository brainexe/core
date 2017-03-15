<?php

namespace BrainExe\Core\Annotations\Builder;

use BrainExe\Core\DependencyInjection\CompilerPass\ConsoleCompilerPass;
use Symfony\Component\DependencyInjection\Definition;

class Command extends ServiceDefinition
{

    /**
     * {@inheritdoc}
     */
    public function setupDefinition(Definition $definition, string $serviceId)
    {
        $definition->addTag(ConsoleCompilerPass::TAG);
        $definition->setPublic(true);
        $definition->setShared(false);

        return sprintf('__console.%s', $serviceId);
    }
}
