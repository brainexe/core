<?php

namespace Matze\Core\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @CompilerPass
 */
class EventListenerCompilerPass implements CompilerPassInterface {

	const TAG = 'event_subscriber';

	/**
	 * {@inheritdoc}
	 */
	public function process(ContainerBuilder $container) {
		$services = $container->findTaggedServiceIds(self::TAG);

		$event_dispatcher = $container->getDefinition('EventDispatcher');

		foreach (array_keys($services) as $service_id) {
			$event_dispatcher->addMethodCall('addSubscriber', [new Reference($service_id)]);
		}
	}
}
