<?php

namespace Matze\Core\DependencyInjection\CompilerPass;

use Assetic\Asset\AssetCollection;
use Matze\Core\Assets\AssetCollector;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @CompilerPass
 */
class AsseticCompilerPass implements CompilerPassInterface {

	const TAG = 'assetic';

	/**
	 * {@inheritdoc}
	 */
	public function process(ContainerBuilder $container) {
		$assetic = $container->getDefinition('Assetic');

		$web_dir = ROOT . '/web/';
		$cache_dir = $web_dir . 'cache';

		// TODO via annotations
		$js_assets = new Definition('Assetic\Asset\AssetCollection', [[
			new Definition('Assetic\Asset\GlobAsset', [$web_dir . 'js/*.js']),
			new Definition('Assetic\Asset\GlobAsset', [$web_dir . 'Bootstrap/js/*.js']),
			new Definition('Assetic\Asset\GlobAsset', [$web_dir . 'rickshaw/d3.*.min.js']),
			new Definition('Assetic\Asset\FileAsset', [$web_dir . 'rickshaw/rickshaw.min.js']),
		]]);
		$js_assets->addMethodCall('setTargetPath', ['merged.js']);

		$css_assets = new Definition('Assetic\Asset\AssetCollection', [[
			new Definition('Assetic\Asset\GlobAsset', [$web_dir . 'Bootstrap/css/*.css']),
			new Definition('Assetic\Asset\GlobAsset', [$web_dir . 'css/*.css']),
		]]);
		$css_assets->addMethodCall('setTargetPath', ['merged.css']);

		$assetic->addMethodCall('set', ['js', $js_assets]);
		$assetic->addMethodCall('set', ['css', $css_assets]);
	}
}
