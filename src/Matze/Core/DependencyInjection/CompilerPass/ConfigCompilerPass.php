<?php

namespace Matze\Core\DependencyInjection\CompilerPass;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;

/**
 * @CompilerPass(priority=10)
 */
class ConfigCompilerPass implements CompilerPassInterface {

	/**
	 * {@inheritdoc}
	 */
	public function process(ContainerBuilder $container) {
		$loader = new XmlFileLoader($container, new FileLocator('config'));

		// load container.xml file from all "matze" components
		$config_finder = new Finder();
		$config_finder
			->files()
			->depth(1)
			->in(MATZE_VENDOR_ROOT)
			->name('container.xml');

		foreach ($config_finder as $file) {
			/** @var SplFileInfo $file */
			$loader->load($file->getPathname());
		}

		if (is_dir(ROOT . '/app')) {
			$loader->load(ROOT . '/app/container.xml');
			$loader->load(ROOT . '/app/config.default.xml');
			if (file_exists(ROOT . '/app/config.xml')) {
				$loader->load(ROOT . '/app/config.xml');
			}
		}

	}
}
