<?php

namespace Matze\Core\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @CompilerPass
 */
class SetDefinitionFileCompilerPass implements CompilerPassInterface {

	/**
	 * {@inheritdoc}
	 */
	public function process(ContainerBuilder $container) {
		foreach ($container->getServiceIds() as $service_id) {
			if (!$container->hasDefinition($service_id)) {
				continue;
			}

			$definition = $container->getDefinition($service_id);
			$reflection_class = new \ReflectionClass($definition->getClass());
			$definition->setFile($reflection_class->getFileName());
		}
	}
}