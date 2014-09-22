<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

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

		$tagged_services = $container->findTaggedServiceIds(self::TAG);
		foreach (array_keys($tagged_services) as $service_id) {
			$definition->addMethodCall('add', [new Reference($service_id)]);
		}
	}
}
