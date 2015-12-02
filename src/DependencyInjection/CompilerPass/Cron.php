<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass;
use BrainExe\Core\Cron\CronDefinition as CronInterface;
use BrainExe\Core\Traits\FileCacheTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @CompilerPass
 */
class Cron implements CompilerPassInterface
{
    use FileCacheTrait;

    const TAG = 'cron';
    const CACHE_FILE = 'crons';

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

        $this->dumpVariableToCache(self::CACHE_FILE, $allCrons);
    }
}
