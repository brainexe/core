<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @CompilerPass(priority=10)
 */
class ConfigCompilerPass implements CompilerPassInterface
{

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $loader     = new XmlFileLoader($container, new FileLocator('config'));
        $filesystem = new FileSystem();

        if ($filesystem->exists(ROOT . 'app')) {
            $loader->load(ROOT . 'app/container.xml');
        } else {
            $loader->load(ROOT . '/container.xml');
        }
    }
}
