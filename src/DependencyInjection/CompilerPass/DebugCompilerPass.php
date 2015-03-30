<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass;
use BrainExe\Core\Redis\RedisLogger;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @CompilerPass(priority=1)
 */
class DebugCompilerPass implements CompilerPassInterface
{

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->getParameter('debug')) {
            return;
        }

        $redis = $container->getDefinition('redis');

        $redisLogger = new Definition(RedisLogger::class, [
            $redis,
            new Reference('monolog.logger')
        ]);
        $redisLogger->setPublic(false);

        $container->setDefinition('redis', $redisLogger);

        foreach ($container->getDefinitions() as $definition) {
//            $definition->setPublic(true);
        }
    }
}
