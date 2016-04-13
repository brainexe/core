<?php

namespace BrainExe\Core\DependencyInjection;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Annotations\Loader;
use BrainExe\Core\Core;
use BrainExe\Core\DependencyInjection\CompilerPass\GlobalCompilerPass;
use Symfony\Bridge\ProxyManager\LazyProxy\PhpDumper\ProxyDumper;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @Service("Core.Rebuild", public=false, shared=false)
 */
class Rebuild
{

    /**
     * @param bool $boot
     * @return Container|ContainerBuilder
     */
    public function rebuildDIC($boot = true)
    {
        $containerBuilder = new ContainerBuilder();

        $this->readAnnotations($containerBuilder);

        $containerBuilder->addCompilerPass(new GlobalCompilerPass());
        $containerBuilder->compile();
        $this->dumpContainer($containerBuilder);

        if ($boot) {
            $core = new Core();
            return $core->boot();
        }

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

        $annotationLoader->load(ROOT . 'src/');

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

        $dumper = new PhpDumper($container);
        $dumper->setProxyDumper(new ProxyDumper());

        $containerContent = $dumper->dump([
            'class' => $className,
            'debug' => $debug
        ]);

        file_put_contents(ROOT . 'cache/dic.php', $containerContent);
        file_put_contents(ROOT . 'cache/dic.txt', $className);
        @chmod($containerFile, 0777);

        file_put_contents(
            ROOT . 'cache/config.json',
            json_encode(
                $container->getParameterBag()->all(),
                JSON_PRETTY_PRINT
            )
        );
    }
}
