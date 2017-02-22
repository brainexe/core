<?php

namespace BrainExe\Core\Annotations\Builder;

use BrainExe\Core\Annotations\Builder\ServiceDefinition;
use BrainExe\Core\Annotations\Listen;
use BrainExe\Core\DependencyInjection\CompilerPass\EventListenerCompilerPass;
use ReflectionMethod;
use Symfony\Component\DependencyInjection\Definition;

class EventListener extends ServiceDefinition
{

    /**
     * {@inheritdoc}
     */
    public function setupDefinition(Definition $definition, string $serviceId)
    {
        $definition->addTag(EventListenerCompilerPass::TAG);
        $definition->setPublic(false);
    }

    /**
     * {@inheritdoc}
     */
    public function processMethod(Definition $definition, ReflectionMethod $method)
    {
        parent::processMethod($definition, $method);

        /** @var Listen $annotation */
        $annotation = $this->reader->getMethodAnnotation($method, Listen::class);
        if ($annotation) {
            $definition->addTag(EventListenerCompilerPass::TAG_METHOD, [
                'method' => $method->getName(),
                'event' => $annotation->event,
                'priority' => $annotation->priority,
            ]);
        }
    }
}
