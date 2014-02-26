<?php

namespace Matze\Core\DependencyInjection\CompilerPass;

use Matze\Core\Controller\ControllerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @CompilerPass
 */
class ControllerCompilerPass implements CompilerPassInterface {

	const TAG = 'controller';

	/**
	 * {@inheritdoc}
	 */
	public function process(ContainerBuilder $container) {
		$all_routes = [];

		$taggedServices = $container->findTaggedServiceIds(self::TAG);
		foreach ($taggedServices as $id => $attributes) {
			/** @var ControllerInterface $service */
			$service = $container->get($id);

			$routes = $service->getRoutes();
			$all_routes = array_merge($all_routes, $routes);
		}

		$routes = $container->getDefinition('RouteCollection');
		foreach ($all_routes as $key => $route) {
			if (!empty($route['requirements'])) {
				$routes->addMethodCall('add', [$key, new Definition('Symfony\Component\Routing\Route', [$route['pattern'], $route['defaults'], $route['requirements']])]);
			} else {
				$routes->addMethodCall('add', [$key, new Definition('Symfony\Component\Routing\Route', [$route['pattern'], $route['defaults']])]);
			}
		}

	}
}
