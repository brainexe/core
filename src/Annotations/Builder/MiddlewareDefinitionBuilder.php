<?php

namespace BrainExe\Core\Annotations\Builder;

use BrainExe\Annotations\Loader\Annotation\DefinitionBuilder\ServiceDefinitionBuilder;
use BrainExe\Core\Annotations\CompilerPass;
use BrainExe\Core\DependencyInjection\CompilerPass\MiddlewareCompilerPass;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Definition;

class MiddlewareDefinitionBuilder extends ServiceDefinitionBuilder
{
    /**
     * @param ReflectionClass $reflectionClass
     * @param CompilerPass $annotation
     * @return array
     */
    public function build(ReflectionClass $reflectionClass, $annotation)
    {
        $definitionHolder = parent::build($reflectionClass, $annotation);

        /** @var Definition $definition */
        $definition = $definitionHolder['definition'];

        $definition->setPublic(false);
        $definition->addTag(MiddlewareCompilerPass::TAG, ['priority' => $annotation->priority]);

        return [
        'id' => $definitionHolder['id'],
        'definition' => $definition
        ];
    }
}
