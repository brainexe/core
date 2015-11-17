<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass;
use BrainExe\Core\Cron\CronDefinition as CronInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @CompilerPass
 */
class Cron implements CompilerPassInterface
{

    const TAG = 'cron';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $allCrons = [];

        $taggedServices = $container->findTaggedServiceIds(self::TAG);
        foreach (array_keys($taggedServices) as $serviceId) {
            /** @var CronInterface $cron */
            $definition = $container->getDefinition($serviceId);
            $cron = $definition->getClass();
            $allCrons = array_merge($allCrons, $cron::getCrons());
        }

        file_put_contents(ROOT . 'cache/crons.php', "<?php return " . var_export($allCrons, true) . ";");
    }
}
