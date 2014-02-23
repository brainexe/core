<?php

namespace Matze\Core\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @CompilerPass
 */
class TwigExtensionCompilerPass implements CompilerPassInterface {

	public function process(ContainerBuilder $container) {
		/** @var \Twig_Environment $twig_definition */
		$twig_definition = $container->getDefinition('Twig');

		$taggedServices = $container->findTaggedServiceIds('twig_extension');
		foreach ($taggedServices as $id => $attributes) {
			$service = $container->getDefinition($id);
			$service->setPublic(false);

			$twig_definition->addMethodCall('addExtension', [new Reference($id)]);
		}
	}
}