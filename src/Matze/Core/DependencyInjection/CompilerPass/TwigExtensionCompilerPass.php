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
		/** @var Definition $twig_compiler_definition */
		$twig_definition = $container->getDefinition('Twig');
		$twig_compiler_definition = $container->getDefinition('TwigCompiler');

		$tagged_services = $container->findTaggedServiceIds(self::TAG);

		$debug = $container->getParameter('debug');
		foreach ($tagged_services as $id => $tag) {
			$service = $container->getDefinition($id);
			$service->setPublic(false);

			if (!$debug && $tag[0]['compiler']) {
				$twig_compiler_definition->addMethodCall('addExtension', [new Reference($id)]);
			} else {
				$twig_definition->addMethodCall('addExtension', [new Reference($id)]);
			}
		}

		if ($debug) {
			$twig_definition->addMethodCall('addExtension', [new Definition('Twig_Extension_Debug')]);
			$twig_definition->addMethodCall('enableStrictVariables');
		}
	}
}
