<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\Route;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Routing\Generator\Dumper\PhpGeneratorDumper;
use Symfony\Component\Routing\Matcher\Dumper\PhpMatcherDumper;

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

			if ($route->isCsrf()) {
				$route->setOptions(['csrf' => true]);
			}

			$routes->addMethodCall('add', [$name, new Definition('Symfony\Component\Routing\Route', [$route->getPath(), $route->getDefaults(), $route->getRequirements(), $route->getOptions(), $route->getHost(), $route->getSchemes(), $route->getMethods(), $route->getCondition()])]);
		}
		self::$routes = [];

		$router_file = sprintf('%scache/router_matcher.php', ROOT);
		$route_dumper = new PhpMatcherDumper($container->get('RouteCollection'));
		$content = $route_dumper->dump();
		file_put_contents($router_file, $content);

		$router_file = sprintf('%scache/router_generator.php', ROOT);
		$route_dumper = new PhpGeneratorDumper($container->get('RouteCollection'));
		$content = $route_dumper->dump();
		file_put_contents($router_file, $content);
	}
}
