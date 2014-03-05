<?php

namespace Matze\Core\DependencyInjection\CompilerPass;

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
		foreach (array_keys($taggedServices) as $id) {
			$service = $container->getDefinition($id);
			$service->setPublic(false);

			$twig_definition->addMethodCall('addExtension', [new Reference($id)]);
		}

		if ($container->getParameter('debug')) {
			$twig_definition->addMethodCall('addExtension', [new Definition('Twig_Extension_Debug')]);
			$twig_definition->addMethodCall('enableStrictVariables');
		}
	}
}
