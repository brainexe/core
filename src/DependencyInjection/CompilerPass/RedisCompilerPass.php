<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass;
use Predis\Connection\Aggregate\MasterSlaveReplication;
use Predis\Replication\ReplicationStrategy;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;

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

        $masterArguments = $this->getArguments($container, '');

        try {
            $slaveArguments  = $this->getArguments($container, '.slave');
            $masterArguments['alias'] = 'master';
            $slaveArguments['alias'] = 'slave';
            $parameters = [$masterArguments, $slaveArguments];
            $options    = [
                'replication' =>
                    new Definition(MasterSlaveReplication::class, [
                        new Definition(ReplicationStrategy::class)
                    ])
            ];

            $redis->setArguments([$parameters, $options]);
        } catch (ParameterNotFoundException $e) {
            // master only
            $redis->setArguments([$masterArguments]);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param $prefix
     * @return string[]
     */
    protected function getArguments(ContainerBuilder $container, $prefix)
    {
        $password = $container->getParameter("redis$prefix.password");
        $database = $container->getParameter("redis$prefix.database");
        $host     = $container->getParameter("redis$prefix.host");
        $port     = $container->getParameter("redis$prefix.port");

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
            return $arguments;
        }

        return $arguments;
    }
}
