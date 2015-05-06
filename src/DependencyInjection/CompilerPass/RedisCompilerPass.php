<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass;
use BrainExe\Core\Redis\Predis;
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
        $class = $redis->getClass();

        $password = $container->getParameter('redis.password');
        $database = $container->getParameter('redis.database');
        $host     = $container->getParameter('redis.host');

        if ($class === Predis::class) {
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

            $redis->setArguments([$arguments]);
        }
    }
}
