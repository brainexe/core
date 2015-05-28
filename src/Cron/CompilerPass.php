<?php

namespace BrainExe\Core\Cron;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use BrainExe\Core\Annotations\CompilerPass as CompilerPassAnnotation;

/**
 * @CompilerPassAnnotation
 */
class CompilerPass implements CompilerPassInterface
{
    const TAG = 'console';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        /*
        $cron = $container->getDefinition('Cron');

        $taggedServices = $container->findTaggedServiceIds(self::TAG);

        $commands = [];
        foreach (array_keys($taggedServices) as $serviceId) {
            $commands[] = new Reference($serviceId);
        }

        $cron->addMethodCall('addCommands', [$commands]);
        */
    }
}
