<?php

namespace BrainExe\Core\DependencyInjection;

use BrainExe\Core\AnnotationLoader;
use BrainExe\Core\Annotations\Service;
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
        $annotationLoader = new AnnotationLoader($container);
        $annotationLoader->load(ROOT . 'src');

        if (!is_dir(ROOT . 'vendor/brainexe/')) {
            return;
        }

        $appFinder = new Finder();
        $appFinder->directories()
            ->in([ROOT . 'vendor/brainexe/'])
            ->depth('<=1')
            ->name('src');

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
        $containerFile = ROOT . 'cache/dic.php';
        $configFile    = ROOT . 'cache/config.json';

        $dumper = new PhpDumper($container);
        $dumper->setProxyDumper(new ProxyDumper());

        $containerContent = $dumper->dump([
            'class' => 'DumpedContainer',
            'debug' => $debug
        ]);

        file_put_contents($containerFile, $containerContent);
        file_put_contents(
            $configFile,
            json_encode(
                $container->getParameterBag()->all(),
                JSON_PRETTY_PRINT
            )
        );
    }
}
