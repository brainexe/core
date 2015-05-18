<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass;
use BrainExe\Core\Redis\RedisScript;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @CompilerPass
 */
class RedisScriptCompilerPass implements CompilerPassInterface
{

    const TAG = 'redis_script';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $redis = $container->getDefinition('redis');

        $taggedServices = $container->findTaggedServiceIds(self::TAG);
        foreach (array_keys($taggedServices) as $serviceId) {
            $definition = $container->getDefinition($serviceId);
            /** @var RedisScript $script */
            $script = $container->get($serviceId);

            $redis->addMethodCall('defineCommand', [
                $script->getName(),
                $definition->getClass()
            ]);
        }
    }
}
