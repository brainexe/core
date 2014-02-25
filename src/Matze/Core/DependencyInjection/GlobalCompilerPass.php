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
		$container->setParameter('application.root', ROOT);

		$service_ids = $container->findTaggedServiceIds(self::TAG);
		$service_priorities = [];
		foreach ($service_ids as $service_id => $tag) {
			$service_priorities[$service_id] = $tag[0]['priority'];
		}

		asort($service_priorities);
		$service_priorities = array_reverse($service_priorities);

		foreach (array_keys($service_priorities) as $service_id) {
			$container->get($service_id)->process($container);
		}
	}
}