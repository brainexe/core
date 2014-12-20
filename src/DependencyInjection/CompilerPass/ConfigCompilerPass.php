<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

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
        $container->setParameter('core_standalone', CORE_STANDALONE);

        $xml_loader = new XmlFileLoader($container, new FileLocator('config'));
        $finder     = new Finder();
        $filesystem = new FileSystem();

     // load container.xml file from all "brainexe" components
        $finder
        ->files()
        ->depth("<= 1")
        ->in([CORE_ROOT . '/..', ROOT, BRAINEXE_VENDOR_ROOT])
        ->name('container.xml');

        foreach ($finder as $file) {
            /** @var SplFileInfo $file */
            $xml_loader->load($file->getPathname());
        }

        if ($filesystem->exists(ROOT . 'app')) {
            $xml_loader->load(ROOT . 'app/container.xml');
            $xml_loader->load(ROOT . 'app/config.default.xml');
            if (file_exists(ROOT . 'app/config.xml')) {
                $xml_loader->load(ROOT . 'app/config.xml');
            }
        }

    }
}
