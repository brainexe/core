<?php

namespace Matze\Core\DependencyInjection;

use Matze\Annotations\Annotations as DI;
use Matze\Core\Controller\ControllerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;

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

		$content = "<?php\n\nreturn ";
		$content .= var_export($all_routes, true);
		$content .= ';';

		file_put_contents(ROOT.'/cache/routes.php', $content);
	}
}