<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @CompilerPass
 */
class EventListenerCompilerPass implements CompilerPassInterface
{

    const TAG = 'event_subscriber';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $dispatcher = $container->getDefinition('EventDispatcher');
        $services   = $container->findTaggedServiceIds(self::TAG);

        foreach (array_keys($services) as $service_id) {
            /** @var EventSubscriberInterface $class */
            $class = $container->getDefinition($service_id)->getClass();

            foreach ($class::getSubscribedEvents() as $eventName => $params) {
                $this->addEvent($dispatcher, $params, $eventName, $service_id);
            }
        }
    }

    /**
     * @param Definition$dispatcher
     * @param string|array $params
     * @param string $name
     * @param string $serviceId
     */
    private function addEvent(Definition $dispatcher, $params, $name, $serviceId)
    {
        if (is_string($params)) {
            $this->_addListener($dispatcher, $name, $serviceId, $params, 0);
        } elseif (is_string($params[0])) {
            $this->_addListener(
                $dispatcher,
                $name,
                $serviceId,
                $params[0],
                isset($params[1]) ? $params[1] : 0
            );
        } else {
            foreach ($params as $listener) {
                $this->_addListener(
                    $dispatcher,
                    $name,
                    $serviceId,
                    $listener[0],
                    isset($listener[1]) ? $listener[1] : 0
                );
            }
        }
    }

    /**
     * @param Definition $dispatcher
     * @param string $name
     * @param string $service_id
     * @param string $action
     * @param integer $priority
     */
    private function _addListener(Definition $dispatcher, $name, $service_id, $action, $priority = 0)
    {
        $parameters = [$name, [$service_id, $action], $priority];

        $dispatcher->addMethodCall('addListenerService', $parameters);
    }
}
