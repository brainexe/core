<?php

namespace BrainExe\Core\Annotations\Builder;

use BrainExe\Annotations\Builder\ServiceDefinition;
use BrainExe\Core\Annotations\Listen;
use BrainExe\Core\DependencyInjection\CompilerPass\EventListenerCompilerPass;
use Doctrine\Common\Annotations\Annotation;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\DependencyInjection\Definition;

class EventListener extends ServiceDefinition
{

    /**
     * {@inheritdoc}
     */
    public function build(ReflectionClass $reflectionClass, Annotation $annotation)
    {
        /** @var Definition $definition */
        list($serviceId, $definition) = parent::build($reflectionClass, $annotation);

        $definition->addTag(EventListenerCompilerPass::TAG);
        $definition->setPublic(false);

        return [$serviceId, $definition];
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
