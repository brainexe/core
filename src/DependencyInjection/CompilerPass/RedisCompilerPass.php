<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @CompilerPass(priority=9)
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
        $database = $container->getParameter('redis.database');
        $host     = $container->getParameter('redis.host');
        $port     = $container->getParameter('redis.port');

        $arguments = [];

        if ($host) {
            $arguments['host'] = $host;
        }

        if ($password) {
            $arguments['password'] = $password;
        }

        if ($database) {
            $arguments['database'] = $database;
        }
        if ($port) {
            $arguments['port'] = $port;
        }

        $redis->setArguments([$arguments]);
    }
}
