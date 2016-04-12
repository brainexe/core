<?php

namespace BrainExe\Core\Annotations\Builder;

use BrainExe\Annotations\Builder\ServiceDefinition;
use BrainExe\Core\DependencyInjection\CompilerPass\EventListenerCompilerPass;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Definition;

class EventListener extends ServiceDefinition
{

    /**
     * {@inheritdoc}
     */
    public function build(ReflectionClass $reflectionClass, $annotation)
    {
        /** @var Definition $definition */
        list($serviceId, $definition) = parent::build($reflectionClass, $annotation);

        $serviceId = sprintf('__Listener.%s', $serviceId);

        $definition->addTag(EventListenerCompilerPass::TAG);
        $definition->setShared(false);
        $definition->setPublic(true);

        return [$serviceId, $definition];
    }
}
