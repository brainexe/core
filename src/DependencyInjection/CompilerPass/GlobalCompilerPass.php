<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use Monolog\Logger;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class GlobalCompilerPass implements CompilerPassInterface
{

    const TAG = 'compiler_pass';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $container->setParameter('application.root', ROOT);

        $serviceIds = $container->findTaggedServiceIds(self::TAG);
        $servicePriorities = [];

        foreach ($serviceIds as $serviceId => $tag) {
            $servicePriorities[$serviceId] = $tag[0]['priority'];
        }

        asort($servicePriorities);
        $servicePriorities = array_reverse($servicePriorities);

        /** @var Logger $logger */
        $totalTime = 0;
        $loggerStore = [];
        foreach (array_keys($servicePriorities) as $serviceId) {
            $startTime = microtime(true);

            $container->get($serviceId)->process($container);

            $totalTime += $diff = microtime(true) - $startTime;
            $loggerStore[] = sprintf('DIC: %0.2fms %s', $diff * 1000, $serviceId);
        }

        $container->reset();
        /** @var Logger $logger */
        $logger = $container->get('logger');
        foreach ($loggerStore as $log) {
            $logger->debug($log);
        }
        $logger->debug(sprintf("DIC: %0.2fms total time", $totalTime * 1000), ['context' => 'dic']);
    }
}
