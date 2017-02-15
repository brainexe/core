<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass;
use BrainExe\Core\Environment;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

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
        $container->setParameter('application.root', ROOT);

        $locator = new FileLocator([
            ROOT,
            ROOT . 'app/',
        ]);

        $xmlLoader = new XmlFileLoader($container, $locator);
        $xmlLoader->import('container.xml');

        $ymlLoader = new YamlFileLoader($container, $locator);
        $ymlLoader->import('config.yml', null, true);

        $environment = $container->getParameter('environment');
        $container->setParameter('debug', $environment !== Environment::PRODUCTION);
    }
}
