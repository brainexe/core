<?php

namespace Matze\Core\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class GlobalCompilerPass implements CompilerPassInterface {

	const TAG = 'compiler_pass';

	/**
	 * {@inheritdoc}
	 */
	public function process(ContainerBuilder $container) {
		$services = $container->findTaggedServiceIds(self::TAG);

		foreach (array_keys($services) as $service_ids) {
			$container->get($service_ids)->process($container);
		}
	}
}