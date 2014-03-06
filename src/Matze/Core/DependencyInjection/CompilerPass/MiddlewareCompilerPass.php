<?php

namespace Matze\Core\DependencyInjection\CompilerPass;

use Monolog\Logger;
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
			$service_priorities[$service_id] = $tag[0]['priority'];
		}

		asort($service_priorities);
		$service_priorities = array_reverse($service_priorities);

		$routes = $container->getDefinition('AppKernel');
		foreach (array_keys($service_priorities) as $service_id) {
			$routes->addMethodCall('addMiddleware', [new Reference($service_id)]);
		}
	}
}

