<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Twig_Extension_Debug;
use Twig_Loader_Array;

/**
 * @CompilerPass
 */
class TwigExtensionCompilerPass implements CompilerPassInterface {

	const TAG = 'twig_extension';

	/**
	 * {@inheritdoc}
	 */
	public function process(ContainerBuilder $container) {
		/** @var Definition $twig */
		/** @var Definition $twig_compiler */
		$twig = $container->getDefinition('Twig');
		$twig_compiler = $container->getDefinition('TwigCompiler');

		if (CORE_STANDALONE) {
			$twig->setArguments([new Definition(Twig_Loader_Array::class, [[]])]);
		}

		$tagged_services = $container->findTaggedServiceIds(self::TAG);

		$debug = $container->getParameter('debug');
		foreach ($tagged_services as $id => $tag) {
			$service = $container->getDefinition($id);
			$service->setPublic(false);

			if (!$debug && $tag[0]['compiler']) {
				$twig_compiler->addMethodCall('addExtension', [new Reference($id)]);
			} else {
				$twig->addMethodCall('addExtension', [new Reference($id)]);
			}
		}

		if ($debug) {
			$twig->addMethodCall('addExtension', [new Definition(Twig_Extension_Debug::class)]);
			$twig->addMethodCall('enableStrictVariables');
		}
	}
}
