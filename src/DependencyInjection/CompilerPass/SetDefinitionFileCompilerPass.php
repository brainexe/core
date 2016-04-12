<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @CompilerPass(priority=1)
 */
class SetDefinitionFileCompilerPass implements CompilerPassInterface
{

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        foreach ($container->getDefinitions() as $serviceId => $definition) {
            try {
                $reflection = new ReflectionClass($definition->getClass());
            } catch (ReflectionException $e) {
                continue;
            }

            $filename = $reflection->getFileName();
            if (!empty($filename)) {
                $definition->setFile($filename);
            }
        }
    }
}
