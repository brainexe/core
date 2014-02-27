<?php

namespace Matze\Core\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @CompilerPass
 */
class ConsoleCompilerPass implements CompilerPassInterface {

	const TAG = 'console';

	/**
	 * {@inheritdoc}
	 */
	public function process(ContainerBuilder $container) {
		$definition = $container->getDefinition('Console');

		$definition->addMethodCall('setAutoExit', [false]);

		$taggedServices = $container->findTaggedServiceIds(self::TAG);
		foreach ($taggedServices as $id => $attributes) {
			$definition->addMethodCall('add', [new Reference($id)]);
		}
	}
}