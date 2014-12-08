<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @CompilerPass(priority=1)
 */
class TestCompilerPass implements CompilerPassInterface {

	/**
	 * {@inheritdoc}
	 */
	public function process(ContainerBuilder $container) {
		if (!$container->getParameter('core_standalone')) {
			return;
		}

		foreach ($container->getDefinitions() as $definition) {
			$definition->setPublic(true);
		}
	}
}
