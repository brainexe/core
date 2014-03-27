<?php

namespace Matze\Core\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Translation\Dumper\MoFileDumper;
use Symfony\Component\Translation\Dumper\PhpFileDumper;
use Symfony\Component\Translation\Dumper\PoFileDumper;

/**
 * @CompilerPass
 */
class TranslationCompilerPass implements CompilerPassInterface {

	/**
	 * {@inheritdoc}
	 */
	public function process(ContainerBuilder $container) {
		$translator = $container->getDefinition('Translator');
		$lang_path = ROOT . '/lang/';

		if (!is_dir($lang_path)) {
			return;
		}

		$finder = new Finder();
		$finder
			->directories()
			->in($lang_path)
			->depth(0);

		foreach ($finder as $dir) {
			/** @var SplFileInfo $dir */
			$locale = $dir->getRelativePathname();

			$lang_dir = sprintf('%slang/%s/LC_MESSAGES/', ROOT, $locale);
			$translator->addMethodCall('addResource', ['mo', sprintf('%smessages.mo', $lang_dir), $locale]);
		}

	}
}