<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass;
use BrainExe\Core\Environment;
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
        $filesystem = new Filesystem();

        if ($filesystem->exists(ROOT . 'app/container.xml')) {
            $loader->load(ROOT . 'app/container.xml');
        } elseif ($filesystem->exists(ROOT . '/container.xml')) {
            $loader->load(ROOT . '/container.xml');
        }

        if (!$container->hasParameter('debug')) {
            $environment = $container->getParameter('environment');
            $container->setParameter('debug', $environment !== Environment::PRODUCTION);
        }
    }
}
