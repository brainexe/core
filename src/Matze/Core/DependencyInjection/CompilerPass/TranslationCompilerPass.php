<?php

namespace Matze\Core\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Translation\Dumper\PhpFileDumper;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

/**
 * @CompilerPass
 */
class TranslationCompilerPass implements CompilerPassInterface {

	/**
	 * {@inheritdoc}
	 */
	public function process(ContainerBuilder $container) {
		$translator = $container->getDefinition('Translator');

		$cache_path = ROOT . '/cache/lang/';

		if (!is_dir(ROOT . '/lang/')) {
			return;
		}

		$finder = new Finder();
		$finder
			->files()
			->in(ROOT . '/lang/')
			->name('*.yaml');

		$yaml_loader = new YamlFileLoader();
		$dumper = new PhpFileDumper();

		foreach ($finder as $file) {
			/** @var SplFileInfo $file */
			$locale = basename($file->getFilename(), '.yaml');
			$resource = $yaml_loader->load($file->getPathname(), $locale);

			$dumper->dump($resource, [
				'path' => $cache_path
			]);

			$translator->addMethodCall('addResource', ['php', sprintf('%smessages.%s.php', $cache_path, $locale), $locale]);
		}

	}
}