<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass;
use BrainExe\Core\EventDispatcher\AbstractEvent;
use Exception;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @CompilerPass
 */
class EventCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!is_dir(ROOT . 'cache')) {
            return;
        }

        $events = [];
        foreach (get_declared_classes() as $class) {
            $reflection = new ReflectionClass($class);

            if (!$reflection->isSubclassOf(AbstractEvent::class)) {
                continue;
            }

            foreach (array_values($reflection->getConstants()) as $constant) {
                if (strlen($constant) < 2) {
                    continue;
                }
                if (isset($events[$constant])) {
                    throw new Exception(
                        sprintf(
                            'Event "%s" was already defined in "%s". (%s)',
                            $constant,
                            $events[$constant],
                            $class
                        )
                    );
                }
                $events[$constant] = $class;
            };
        }

        $content = '<?php return ' . var_export($events, true) . ';';
        $file  = sprintf('%scache/events.php', ROOT);
        file_put_contents($file, $content);
    }
}
