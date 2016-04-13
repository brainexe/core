<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @CompilerPass
 */
class MergeDefinitions implements CompilerPassInterface
{
    const TAG = 'merge_definition';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $services = $container->findTaggedServiceIds(self::TAG);
        foreach ($services as $serviceId => $tags) {
            $child  = $container->getDefinition($serviceId);
            $parent = $container->getDefinition($tags[0]['parent']);

            foreach ($child->getMethodCalls() as list($method, $arguments)) {
                $parent->addMethodCall($method, $arguments);
            }
            $container->removeDefinition($serviceId);
        }
    }
}
