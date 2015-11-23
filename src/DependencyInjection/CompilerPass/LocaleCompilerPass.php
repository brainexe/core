<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass;

use BrainExe\Core\Util\Glob;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @CompilerPass
 */
class LocateCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!is_dir(ROOT . 'lang')) {
            return;
        }

        /** @var Glob $glob */
        $glob = $container->get('Glob');

        $locales = $glob->execGlob(ROOT . 'lang/*.po');
        $locales = array_map(function ($file) {
            return basename($file, '.po');
        }, $locales);

        array_unshift($locales, 'en'); // EN is default locale

        $container->setParameter('locales', $locales);
    }
}
