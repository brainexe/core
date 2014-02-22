<?php

namespace Matze\Core\DependencyInjection;

use Matze\Annotations\Annotations as DI;
use Matze\Core\Controller\ControllerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @Service(tags={{"name" = "compiler_pass"}})
 */
class ControllerCompilerPass implements CompilerPassInterface {

	public function process(ContainerBuilder $container) {
		$definition = $container->getDefinition('Application');

		$taggedServices = $container->findTaggedServiceIds('controller');
		foreach ($taggedServices as $id => $attributes) {
			/** @var ControllerInterface $service */
			$service = $container->get($id);

			$definition->addMethodCall('mount', [$service->getPath(), new Reference($id)]);
		}
	}
}