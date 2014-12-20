<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @CompilerPass
 */
class RedisCompilerPass implements CompilerPassInterface
{

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $redis = $container->getDefinition('redis');

        if ($password = $container->getParameter('redis.password')) {
            $redis->addMethodCall('auth', [$password]);
        }

        if ($database = $container->getParameter('redis.database')) {
            $redis->addMethodCall('select', [$database]);
        }
    }
}
