<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @CompilerPass
 */
class SetDefinitionFileCompilerPass implements CompilerPassInterface
{

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        foreach ($container->getServiceIds() as $serviceId) {
            if (!$container->hasDefinition($serviceId)) {
                continue;
            }

            $definition = $container->getDefinition($serviceId);

            try {
                $reflection = new ReflectionClass($definition->getClass());
            } catch (ReflectionException $e) {
                continue;
            }

            $filename = $reflection->getFileName();
            if (empty($filename)) {
                continue;
            }

            $definition->setFile($filename);
        }
    }
}
