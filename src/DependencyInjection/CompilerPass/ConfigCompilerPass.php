<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;


use BrainExe\Core\Annotations\CompilerPass;
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

        $loader     = new XmlFileLoader($container, new FileLocator('config'));
        $finder     = new Finder();
        $filesystem = new FileSystem();

        // load container.xml file from all "brainexe" components
        $finder
            ->files()
            ->depth('<= 1')
            ->in([CORE_ROOT . '/..', ROOT, BRAINEXE_VENDOR_ROOT])
            ->name('container.xml');

        foreach ($finder as $file) {
            /** @var SplFileInfo $file */
            $loader->load($file->getPathname());
        }

        if ($filesystem->exists(ROOT . 'app')) {
            $loader->load(ROOT . 'app/container.xml');
            $loader->load(ROOT . 'app/config.default.xml');
            if (file_exists(ROOT . 'app/config.xml')) {
                $loader->load(ROOT . 'app/config.xml');
            }
        }

        // store json-config
        $debug = $container->getParameter('debug');
        file_put_contents(
            ROOT . 'cache/config.json',
            json_encode(
                $container->getParameterBag()->all(),
                $debug ? JSON_PRETTY_PRINT : null
            )
        );
    }
}
