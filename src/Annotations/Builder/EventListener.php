<?php

namespace BrainExe\Core\Annotations\Builder;

use BrainExe\Annotations\Loader\Annotation\ServiceDefinitionBuilder;
use BrainExe\Core\DependencyInjection\CompilerPass\EventListenerCompilerPass;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Definition;

class EventListener extends ServiceDefinitionBuilder
{

    /**
     * {@inheritdoc}
     */
    public function build(ReflectionClass $reflectionClass, $annotation)
    {
        /** @var Definition $definition */
        list($serviceId, $definition) = parent::build($reflectionClass, $annotation);

        $serviceId = sprintf('__Listener.%s', str_replace('Listener', '', $serviceId));

        $definition->addTag(EventListenerCompilerPass::TAG);

        return [$serviceId, $definition];
    }
}
