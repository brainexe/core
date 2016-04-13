<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
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
            $slaveArguments = $this->getArguments($container, '.slave');
            $parameters = [$masterArguments, $slaveArguments];
            $options    = ['replication' => true];

            $redis->setArguments([$parameters, $options]);
        } catch (ParameterNotFoundException $e) {
            // master only
            $redis->setArguments([$masterArguments]);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param string $prefix
     * @return string
     */
    protected function getArguments(ContainerBuilder $container, string $prefix) : string
    {
        return $container->getParameter("redis$prefix.connection");
    }
}
