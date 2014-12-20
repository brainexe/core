<?php

namespace BrainExe\Core\Annotations\Builder;

use BrainExe\Annotations\Loader\Annotation\DefinitionBuilder\ServiceDefinitionBuilder;
use BrainExe\Core\DependencyInjection\CompilerPass\EventListenerCompilerPass;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Definition;

class EventListenerDefinitionBuilder extends ServiceDefinitionBuilder
{

    /**
     * {@inheritdoc}
     */
    public function build(ReflectionClass $reflection_class, $annotation)
    {
        $definitionHolder = parent::build($reflection_class, $annotation);

        /** @var Definition $definition */
        $definition = $definitionHolder['definition'];

        $id = sprintf('__Listener.%s', str_replace('Listener', '', $definitionHolder['id']));

        $definition->addTag(EventListenerCompilerPass::TAG);

        return [
        'id' => $id,
        'definition' => $definition
        ];
    }
}
