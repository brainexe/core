<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass;
use BrainExe\Core\Redis\RedisScriptInterface;
use Exception;
use ReflectionClass;
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
        $redisScript = $container->getDefinition('RedisScripts');

        $taggedServices = $container->findTaggedServiceIds(self::TAG);
        foreach (array_keys($taggedServices) as $serviceId) {
            $definition = $container->getDefinition($serviceId);
            /** @var RedisScriptInterface $class */
            $class = $definition->getClass();

            $reflection = new ReflectionClass($class);
            if (!$reflection->implementsInterface(RedisScriptInterface::class)) {
                throw new Exception(sprintf(
                    "Class %s dies not implements the interface 'RedisScriptInterface'",
                    $class
                ));
            }
            $scripts = $class::getRedisScripts();

            foreach ($scripts as $name => $script) {
                $sha1 = sha1($script);
                $redisScript->addMethodCall('registerScript', [$name, $sha1, $script]);
            }
        }
    }
}
