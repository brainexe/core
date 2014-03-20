<?php

namespace Matze\Core\DependencyInjection\CompilerPass;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
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
		$xml_loader = new XmlFileLoader($container, new FileLocator('config'));
		$yaml_loader = new YamlFileLoader($container, new FileLocator('config'));

		// load container.xml file from all "matze" components
		$config_finder = new Finder();
		$config_finder
			->files()
			->depth(1)
			->in(MATZE_VENDOR_ROOT)
			->name('container.xml');

		foreach ($config_finder as $file) {
			/** @var SplFileInfo $file */
			$xml_loader->load($file->getPathname());
		}

		if (is_dir(ROOT . '/app')) {
			$xml_loader->load(ROOT . '/app/container.xml');
			$xml_loader->load(ROOT . '/app/config.default.xml');
			$yaml_loader->load(ROOT . '/app/assets.yaml');
			if (file_exists(ROOT . '/app/config.xml')) {
				$xml_loader->load(ROOT . '/app/config.xml');
			}
		}

	}
}
