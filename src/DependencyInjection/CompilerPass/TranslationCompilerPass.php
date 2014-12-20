<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @CompilerPass
 * @codeCoverageIgnore
 */
class TranslationCompilerPass implements CompilerPassInterface
{

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $langPath = ROOT . '/lang/';

        if (!is_dir($langPath)) {
            return;
        }

        $finder = new Finder();
        $finder
            ->directories()
            ->in($langPath)
            ->depth(0);

//        foreach ($finder as $directory) {
//            /** @var SplFileInfo $directory */
//      			$locale = $dir->getRelativePathname();
//      			$lang_dir = sprintf('%slang/%s/LC_MESSAGES/', ROOT, $locale);
//      			$translator->addMethodCall('addResource', ['mo', sprintf('%smessages.mo', $lang_dir), $locale]);
//        }

    }
}
