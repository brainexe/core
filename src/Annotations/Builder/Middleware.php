<?php

namespace BrainExe\Core\Annotations\Builder;

use BrainExe\Core\DependencyInjection\CompilerPass\MiddlewareCompilerPass;
use Symfony\Component\DependencyInjection\Definition;

class Middleware extends ServiceDefinition
{
    /**
     * {@inheritdoc}
     */
    public function setupDefinition(Definition $definition, string $serviceId)
    {
        $definition->addTag(MiddlewareCompilerPass::TAG);
        $definition->setPublic(false);
    }
}
