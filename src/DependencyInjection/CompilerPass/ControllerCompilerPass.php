<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\Route as RouteAnnotation;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Routing\Generator\Dumper\PhpGeneratorDumper;
use Symfony\Component\Routing\Matcher\Dumper\PhpMatcherDumper;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * @CompilerPass
 */
class ControllerCompilerPass implements CompilerPassInterface {

	/**
	 * @todo why static?
	 * @var RouteAnnotation[]
	 */
	private static $routes = [];

	/**
	 * @param RouteAnnotation $route
	 */
	public static function addRoute(RouteAnnotation $route) {
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

			$router_definition = new Definition(Route::class, [
				$route->getPath(),
				$route->getDefaults(),
				$route->getRequirements(),
				$route->getOptions(),
				$route->getHost(),
				$route->getSchemes(),
				$route->getMethods(),
				$route->getCondition()
			]);
			$routes->addMethodCall('add', [$name, $router_definition]);
		}

		self::$routes = [];

		/** @var RouteCollection $router_collection */
		$router_collection = $container->get('RouteCollection');

		$router_file  = sprintf('%scache/router_matcher.php', ROOT);
		$route_dumper = new PhpMatcherDumper($router_collection);
		$content      = $route_dumper->dump();
		file_put_contents($router_file, $content);

		$router_file  = sprintf('%scache/router_generator.php', ROOT);
		$route_dumper = new PhpGeneratorDumper($router_collection);
		$content      = $route_dumper->dump();
		file_put_contents($router_file, $content);
	}
}
