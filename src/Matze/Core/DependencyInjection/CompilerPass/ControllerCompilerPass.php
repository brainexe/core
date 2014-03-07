<?php

namespace Matze\Core\DependencyInjection\CompilerPass;

use Matze\Core\Annotations\Route;
use Matze\Core\Controller\ControllerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @CompilerPass
 */
class ControllerCompilerPass implements CompilerPassInterface {

	/**
	 * @var Route[]
	 */
	private static $routes = [];

	/**
	 * @param Route $route
	 */
	public static function addRoute(Route $route) {
		self::$routes[] = $route;
	}

	const TAG = 'controller';

	/**
	 * {@inheritdoc}
	 */
	public function process(ContainerBuilder $container) {
		$routes = $container->getDefinition('RouteCollection');

		foreach (self::$routes as $route) {
			$name = $route->getName() ?: str_replace('/', '.', trim($route->getPath(), './'));

			$routes->addMethodCall('add', [$name, new Definition('Symfony\Component\Routing\Route', [$route->getPath(), $route->getDefaults(), $route->getRequirements(), $route->getOptions(), $route->getHost(), $route->getSchemes(), $route->getMethods(), $route->getCondition()])]);
		}
		self::$routes = [];
	}
}
