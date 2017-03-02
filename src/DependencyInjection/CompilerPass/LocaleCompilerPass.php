<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass;
use BrainExe\Core\Util\Glob;
use Exception;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @CompilerPass
 */
class LocaleCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function process(ContainerBuilder $container)
    {
        /** @var Glob $glob */
        $glob = $container->get(Glob::class);

        $locales = $glob->execGlob(ROOT . 'lang/*.po');
        $locales = array_map(function ($file) {
            return basename($file, '.po');
        }, $locales);

        $container->setParameter('locales', $locales);
    }
}
