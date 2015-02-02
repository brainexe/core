<?php

namespace BrainExe\Core\DependencyInjection;

use BrainExe\Annotations\Loader\AnnotationLoader;
use BrainExe\Core\Core;
use BrainExe\Core\DependencyInjection\CompilerPass\GlobalCompilerPass;
use Doctrine\Common\Cache\ArrayCache;
use Redis;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Dumper\XmlDumper;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Doctrine\Common\Cache\RedisCache as DoctrineCache;

/**
 * @service("Core.Rebuild", public=false)
 */
class Rebuild
{

    /**
     * @param bool $boot
     * @return Container|ContainerBuilder
     */
    public function rebuildDIC($boot = true)
    {

        // TODO
        $redis = new Redis();
        $redis->connect('localhost');
        $redis->select(10);

        $cache = new DoctrineCache();
        $cache->setRedis($redis);

        $containerBuilder = new ContainerBuilder();
        $annotationLoader = new AnnotationLoader($containerBuilder, $cache);
        $appFinder        = new Finder();

        $appFinder
            ->directories()
            ->in([ROOT, CORE_ROOT, BRAINEXE_VENDOR_ROOT])
            ->depth("<=1")
            ->name('src');

        foreach ($appFinder as $dir) {
            /** @var SplFileInfo $dir */
            $configFile = $dir->getPathname() . '/../config.php';
            if (is_file($configFile)) {
                require $configFile;
            }
        }

        $annotationLoader->load('src/');
        $annotationLoader->load(CORE_ROOT);

        foreach ($appFinder as $dir) {
            /** @var SplFileInfo $dir */
            $annotationLoader->load($dir->getPathname());
        }

        $containerBuilder->addCompilerPass(new GlobalCompilerPass());
        $containerBuilder->compile();

        $randomId      = mt_rand();
        $containerName = sprintf('dic_%d', $randomId);
        $containerFile = sprintf('cache/dic_%d.php', $randomId);

        foreach (glob('cache/dic_*.php') as $file) {
            unlink($file);
        }

        $dumper            = new PhpDumper($containerBuilder);
        $containerContent  = $dumper->dump(['class' => $containerName]);
        file_put_contents($containerFile, $containerContent);
        chmod($containerFile, 0777);

        $dumper            = new XmlDumper($containerBuilder);
        $containerContent = $dumper->dump();
        file_put_contents('cache/dic.xml', $containerContent);

        if ($boot) {
            $core = new Core();
            return $core->boot();
        }

        return $containerBuilder;
    }
}
