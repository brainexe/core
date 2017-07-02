<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass;
use Exception;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @CompilerPass
 */
class EventListenerCompilerPass implements CompilerPassInterface
{

    const TAG        = 'event_subscriber.class';
    const TAG_METHOD = 'event_subscriber.method';

    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->container = $container;
        $dispatcher = $container->findDefinition('EventDispatcher');

        $services = $container->findTaggedServiceIds(self::TAG);
        foreach (array_keys($services) as $serviceId) {
            /** @var EventSubscriberInterface $class */
            $class = $container->findDefinition($serviceId)->getClass();
            if (method_exists($class, 'getSubscribedEvents')) {
                foreach ($class::getSubscribedEvents() as $eventName => $params) {
                    $this->addEvent($dispatcher, $params, $eventName, $serviceId);
                }
            }
        }

        $this->processMethods($container, $dispatcher);
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
            $this->addListener($dispatcher, $name, $serviceId, $params, 0);
        } elseif (is_string($params[0])) {
            $this->addListener(
                $dispatcher,
                $name,
                $serviceId,
                $params[0],
                isset($params[1]) ? $params[1] : 0
            );
        } else {
            foreach ($params as $listener) {
                $this->addListener(
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
     * @param string $eventName
     * @param string $serviceId
     * @param string $method
     * @param integer $priority
     * @throws Exception
     */
    private function addListener(Definition $dispatcher, $eventName, $serviceId, $method, $priority = 0)
    {
        $parameters = [
            $eventName,
            [new ServiceClosureArgument(new Reference($serviceId)), $method],
            $priority
        ];

        $class = $this->container->findDefinition($serviceId)->getClass();
        if (!method_exists($class, $method)) {
            throw new Exception(sprintf('Invalid event dispatcher method: %s::%s()', $serviceId, $method));
        }

        $dispatcher->addMethodCall('addListener', $parameters);
    }

    /**
     * @param ContainerBuilder $container
     * @param Definition $dispatcher
     */
    private function processMethods(ContainerBuilder $container, Definition $dispatcher)
    {
        $services = $container->findTaggedServiceIds(self::TAG_METHOD);
        foreach ($services as $serviceId => $arguments) {
            foreach ($arguments as $args) {
                $this->addListener(
                    $dispatcher,
                    $args['event'],
                    $serviceId,
                    $args['method'],
                    $args['priority']
                );
            }
        }
    }
}
