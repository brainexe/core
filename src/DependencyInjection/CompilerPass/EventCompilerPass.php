<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass;
use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\Traits\FileCacheTrait;
use Exception;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @CompilerPass
 */
class EventCompilerPass implements CompilerPassInterface
{
    use FileCacheTrait;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->dumpVariableToCache('events', $this->getEvents());
    }

    /**
     * @return array
     * @throws Exception
     */
    private function getEvents()
    {
        $events = [];
        foreach (get_declared_classes() as $class) {
            $reflection = new ReflectionClass($class);

            if ($reflection->isSubclassOf(AbstractEvent::class)) {
                $this->handleEvent($reflection, $events, $class);
            }
        }

        return $events;
    }

    /**
     * @param ReflectionClass $reflection
     * @param $events
     * @param string $class
     * @throws Exception
     */
    private function handleEvent(ReflectionClass $reflection, array &$events, string $class)
    {
        foreach (array_values($reflection->getConstants()) as $constant) {
            if (strlen($constant) < 2) {
                continue;
            }
            if (isset($events[$constant])) {
                throw new Exception(sprintf(
                    'Event "%s" was already defined in "%s". (%s)',
                    $constant,
                    $events[$constant],
                    $class
                ));
            }

            $parameters = $this->getParameters($reflection);

            $events[$constant] = [
                'class'      => $class,
                'parameters' => $parameters
            ];
        }
    }

    /**
     * @param ReflectionClass $reflection
     * @return array
     */
    private function getParameters(ReflectionClass $reflection)
    {
        $parameters = [];
        foreach ($reflection->getConstructor()->getParameters() as $parameter) {
            $parameters[] = $parameter->getName();
        }

        return $parameters;
    }
}
