<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @CompilerPass
 */
class MiddlewareCompilerPass implements CompilerPassInterface
{

    const TAG = 'middleware';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $serviceIds = $container->findTaggedServiceIds(self::TAG);
        $servicePriorities = [];
        foreach ($serviceIds as $serviceId => $tag) {
            if (null === $tag[0]['priority']) {
                continue;
            }
            $servicePriorities[$serviceId] = $tag[0]['priority'];
        }

        asort($servicePriorities);
        $servicePriorities = array_reverse($servicePriorities);

        $appKernel = $container->getDefinition('AppKernel');

        $references = [];
        foreach (array_keys($servicePriorities) as $serviceId) {
            $references[] = new Reference($serviceId);
        }

        $appKernel->addMethodCall('setMiddlewares', [$references]);
    }
}
