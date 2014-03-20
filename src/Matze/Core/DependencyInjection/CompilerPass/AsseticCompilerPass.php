<?php

namespace Matze\Core\DependencyInjection\CompilerPass;

use Assetic\Asset\AssetCollection;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @CompilerPass
 */
class AsseticCompilerPass implements CompilerPassInterface {

	/**
	 * {@inheritdoc}
	 */
	public function process(ContainerBuilder $container) {
		$asset_collector = $container->getDefinition('AssetCollector');

		$asset_collector->addMethodCall('collectAssets', [new Reference('Assetic')]);
	}
}
