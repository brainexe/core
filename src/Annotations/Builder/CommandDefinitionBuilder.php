<?php

namespace BrainExe\Core\Annotations\Builder;

use BrainExe\Annotations\Loader\Annotation\DefinitionBuilder\ServiceDefinitionBuilder;
use BrainExe\Core\DependencyInjection\CompilerPass\ConsoleCompilerPass;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Definition;

class CommandDefinitionBuilder extends ServiceDefinitionBuilder
{
    /**
     * {@inheritdoc}
     */
    public function build(ReflectionClass $reflectionClass, $annotation)
    {
        $definitionHolder = parent::build($reflectionClass, $annotation);

        /** @var Definition $definition */
        $definition = $definitionHolder['definition'];

        $definition->setPublic(false);
        $definition->addTag(ConsoleCompilerPass::TAG);

        return [
        'id' => $definitionHolder['id'],
        'definition' => $definition
        ];
    }
}
