<?php

namespace BrainExe\Core\Annotations\Builder;

use BrainExe\Annotations\Loader\Annotation\DefinitionBuilder\ServiceDefinitionBuilder;
use BrainExe\Core\Annotations\TwigExtension;
use BrainExe\Core\DependencyInjection\CompilerPass\TwigExtensionCompilerPass;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Definition;

class TwigExtensionDefinitionBuilder extends ServiceDefinitionBuilder
{

    /**
     * @param ReflectionClass $reflectionClass
     * @param TwigExtension $annotation
     * @return array
     */
    public function build(ReflectionClass $reflectionClass, $annotation)
    {
        $definitionHolder = parent::build($reflectionClass, $annotation);

        /** @var Definition $definition */
        $definition = $definitionHolder['definition'];

        $definition->addTag(TwigExtensionCompilerPass::TAG, ['compiler' => $annotation->compiler]);
        $definition->setPublic(false);

        return [
        'id' => $definitionHolder['id'],
        'definition' => $definition
        ];
    }
}
