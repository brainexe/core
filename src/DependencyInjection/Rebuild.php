<?php

namespace BrainExe\Core\DependencyInjection;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Annotations\Loader;
use Symfony\Bridge\ProxyManager\LazyProxy\PhpDumper\ProxyDumper;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @Service(shared=false)
 */
class Rebuild
{

    /**
     * @return Container|ContainerBuilder
     */
    public function buildContainer() : Container
    {
        $containerBuilder = new ContainerBuilder();

        $this->readAnnotations($containerBuilder);

        $containerBuilder->compile();
        $this->dumpContainer($containerBuilder);

        return $containerBuilder;
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function readAnnotations(ContainerBuilder $container)
    {
        $annotationLoader = new Loader($container);

        $appFinder = new Finder();
        $appFinder->directories()
            ->in([ROOT . 'vendor/brainexe/'])
            ->depth("<=1")
            ->name('src');

        $annotationLoader->load(ROOT . 'src');

        foreach ($appFinder as $dir) {
            /** @var SplFileInfo $dir */
            $annotationLoader->load($dir->getPathname());
        }
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function dumpContainer(ContainerBuilder $container)
    {
        $debug         = $container->getParameter('debug');
        $randomId      = mt_rand();
        $className     = sprintf('dic_%d', $randomId);
        $containerFile = ROOT . 'cache/dic.php';
        $versionFile   = ROOT . 'cache/dic.txt';
        $configFile    = ROOT . 'cache/config.json';

        $dumper = new PhpDumper($container);
        $dumper->setProxyDumper(new ProxyDumper());

        $containerContent = $dumper->dump([
            'class' => $className,
            'debug' => $debug
        ]);

        file_put_contents($containerFile, $containerContent);
        file_put_contents($versionFile, $className);
        file_put_contents(
            $configFile,
            json_encode(
                $container->getParameterBag()->all(),
                JSON_PRETTY_PRINT
            )
        );

        @chmod($containerFile, 0777);
        @chmod($versionFile, 0777);
        @chmod($configFile, 0777);
    }
}
