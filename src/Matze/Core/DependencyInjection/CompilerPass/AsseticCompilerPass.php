<?php

namespace Matze\Core\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @CompilerPass
 */
class AsseticCompilerPass implements CompilerPassInterface {

	/**
	 * {@inheritdoc}
	 */
	public function process(ContainerBuilder $container) {
		$assetic = $container->getDefinition('Assetic');
		$assetic->setPublic(true);


		$assetic->addMethodCall('set', ['js', new Definition('Assetic\Asset\AssetCollection', [[new Definition('Assetic\Asset\GlobAsset', [ROOT . 'static/js'])]])]);
	}
}