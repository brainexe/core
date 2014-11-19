<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @CompilerPass
 */
class EventListenerCompilerPass implements CompilerPassInterface {

	const TAG = 'event_subscriber';

	/**
	 * {@inheritdoc}
	 */
	public function process(ContainerBuilder $container) {
		$dispatcher = $container->getDefinition('EventDispatcher');
		$services   = $container->findTaggedServiceIds(self::TAG);

		foreach (array_keys($services) as $service_id) {
			/** @var EventSubscriberInterface $subscriber */
			$subscriber = $container->get($service_id);

			foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
				if (is_string($params)) {
					$this->_addListener($dispatcher, $eventName, $service_id, $params, 0);
				} elseif (is_string($params[0])) {
					$this->_addListener($dispatcher, $eventName, $service_id, $params[0], isset($params[1]) ? $params[1] : 0);
				} else {
					foreach ($params as $listener) {
						$this->_addListener($dispatcher, $eventName, $service_id, $listener[0], isset($listener[1]) ? $listener[1] : 0);
					}
				}
			}
		}
	}

	/**
	 * @param Definition $event_dispatcher
	 * @param string $event_name
	 * @param string $service_id
	 * @param string $action
	 * @param integer $priority
	 */
	private function _addListener(Definition $event_dispatcher, $event_name, $service_id, $action, $priority = 0) {
		$parameters = [$event_name, [$service_id, $action], $priority];

		$event_dispatcher->addMethodCall('addListenerService', $parameters);
	}
}
