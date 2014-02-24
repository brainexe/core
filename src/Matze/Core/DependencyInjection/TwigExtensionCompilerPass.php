<?php

namespace Matze\Core\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @CompilerPass
 */
class TwigExtensionCompilerPass implements CompilerPassInterface {

	const TAG = 'twig_extension';

	/**
	 * {@inheritdoc}
	 */
	public function process(ContainerBuilder $container) {
		/** @var Definition $twig_definition */
		$twig_definition = $container->getDefinition('Twig');

		$taggedServices = $container->findTaggedServiceIds(self::TAG);
		foreach ($taggedServices as $id => $attributes) {
			$service = $container->getDefinition($id);
			$service->setPublic(false);

			$twig_definition->addMethodCall('addExtension', [new Reference($id)]);
		}
	}
}