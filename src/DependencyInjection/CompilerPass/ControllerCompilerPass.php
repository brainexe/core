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

	const CONTROLLER_TAG = 'controller';
	const ROUTE_TAG = 'route';

	/**
	 * {@inheritdoc}
	 */
	public function process(ContainerBuilder $container) {
		$route_collector = $container->getDefinition('RouteCollection');

		$controllers = $container->findTaggedServiceIds(self::ROUTE_TAG);

		foreach ($controllers as $id => $tag) {
			foreach ($tag as $route_raw) {
				/** @var RouteAnnotation $route */
				$route = $route_raw[0];

				$name = $route->getName() ?: md5($route->getPath());

				$router_definition = $this->_createDefinition($route);
				$route_collector->addMethodCall('add', [$name, $router_definition]);
			}

			$controller = $container->getDefinition($id);
			$controller->clearTag(self::ROUTE_TAG);
		}

		$this->_dumpMatcher($container);
	}

	/**
	 * @param RouteAnnotation $route
	 * @return Definition
	 */
	private function _createDefinition(RouteAnnotation $route) {
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

		return $router_definition;
	}

	/**
	 * @param ContainerBuilder $container
	 * @codeCoverageIgnore
	 */
	protected function _dumpMatcher(ContainerBuilder $container) {
		if (!is_dir(ROOT . 'cache')) {
			return;
		}

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
