<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass;
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

        $password = $container->getParameter('redis.password');
        if (!empty($password)) {
            $redis->addMethodCall('auth', [$password]);
        }

        $database = $container->getParameter('redis.database');
        if (!empty($database)) {
            $redis->addMethodCall('select', [$database]);
        }
    }
}
