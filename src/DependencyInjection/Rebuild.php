<?php

namespace BrainExe\Core\DependencyInjection;

use BrainExe\Annotations\Loader\AnnotationLoader;
use BrainExe\Core\Core;
use BrainExe\Core\DependencyInjection\CompilerPass\GlobalCompilerPass;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Dumper\XmlDumper;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

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
        $container_builder = new ContainerBuilder();
        $annotation_loader = new AnnotationLoader($container_builder);
        $app_finder        = new Finder();

        $app_finder
        ->directories()
        ->in([ROOT, CORE_ROOT, BRAINEXE_VENDOR_ROOT])
        ->depth("<=1")
        ->name('src');

        foreach ($app_finder as $dir) {
            /** @var SplFileInfo $dir */
            $config_file = $dir->getPathname() . '/../config.php';
            if (is_file($config_file)) {
                require $config_file;
            }
        }

        $annotation_loader->load('src/');
        $annotation_loader->load(CORE_ROOT);

        foreach ($app_finder as $dir) {
            /** @var SplFileInfo $dir */
            $annotation_loader->load($dir->getPathname());
        }

        $container_builder->addCompilerPass(new GlobalCompilerPass());
        $container_builder->compile();

        $random_id      = mt_rand();
        $container_name = sprintf('dic_%d', $random_id);
        $container_file = sprintf('cache/dic_%d.php', $random_id);

        foreach (glob('cache/dic_*.php') as $file) {
            unlink($file);
        }

        $dumper            = new PhpDumper($container_builder);
        $container_content = $dumper->dump(['class' => $container_name]);
        file_put_contents($container_file, $container_content);
        chmod($container_file, 0777);

        $dumper            = new XmlDumper($container_builder);
        $container_content = $dumper->dump();
        file_put_contents('cache/dic.xml', $container_content);

        if ($boot) {
            return Core::boot();
        }

        return $container_builder;
    }
}
