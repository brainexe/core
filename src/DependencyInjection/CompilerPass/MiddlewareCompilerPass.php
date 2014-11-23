<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @CompilerPass
 */
class MiddlewareCompilerPass implements CompilerPassInterface {

	const TAG = 'middleware';

	/**
	 * {@inheritdoc}
	 */
	public function process(ContainerBuilder $container) {
		$service_ids = $container->findTaggedServiceIds(self::TAG);
		$service_priorities = [];
		foreach ($service_ids as $service_id => $tag) {
			if (null === $tag[0]['priority']) {
				continue;
			}
			$service_priorities[$service_id] = $tag[0]['priority'];
		}

		asort($service_priorities);
		$service_priorities = array_reverse($service_priorities);

		$app_kernel = $container->getDefinition('AppKernel');

		$references = [];
		foreach (array_keys($service_priorities) as $service_id) {
			$references[] = new Reference($service_id);
		}

		$app_kernel->addMethodCall('setMiddlewares', [$references]);
	}
}

