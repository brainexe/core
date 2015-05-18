<?php

namespace BrainExe\Core\DependencyInjection;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Annotations\Loader;
use BrainExe\Core\Core;
use BrainExe\Core\DependencyInjection\CompilerPass\GlobalCompilerPass;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @Service("Core.Rebuild", public=false)
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
        $annotationLoader = new Loader($containerBuilder);
        $appFinder        = new Finder();

        $appFinder
            ->directories()
            ->in([ROOT, CORE_ROOT, BRAINEXE_VENDOR_ROOT])
            ->depth("<=1")
            ->name('src');

        $annotationLoader->load('src/');
        $annotationLoader->load(CORE_ROOT);

        foreach ($appFinder as $dir) {
            /** @var SplFileInfo $dir */

            $dirName = $dir->getPathname();
            if ($dirName === CORE_ROOT) {
                continue;
            }
            $annotationLoader->load($dirName);
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

        if ($boot) {
            $core = new Core();
            return $core->boot();
        }

        return $containerBuilder;
    }
}
