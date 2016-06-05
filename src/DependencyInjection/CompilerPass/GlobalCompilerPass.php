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

        $servicePriorities = $this->loadCompilerPasses($container);

        /** @var Logger $logger */
        $totalTime = 0;
        $loggerStore = [];
        foreach (array_keys($servicePriorities) as $serviceId) {
            $startTime = microtime(true);

            $container->get($serviceId)->process($container);

            $totalTime += $diff = microtime(true) - $startTime;
            $loggerStore[] = sprintf('DIC: %0.2fms %s', $diff * 1000, $serviceId);
        }

        $this->logResult($container, $loggerStore, $totalTime);
    }

    /**
     * @param ContainerBuilder $container
     * @return array
     */
    private function loadCompilerPasses(ContainerBuilder $container)
    {
        $serviceIds        = $container->findTaggedServiceIds(self::TAG);
        $servicePriorities = [];

        foreach ($serviceIds as $serviceId => $tag) {
            $servicePriorities[$serviceId] = $tag[0]['priority'];
        }

        arsort($servicePriorities);

        return $servicePriorities;
    }

    /**
     * @param ContainerBuilder $container
     * @param array $loggerStore
     * @param float $totalTime
     */
    private function logResult(ContainerBuilder $container, array $loggerStore, float $totalTime)
    {
        $container->reset();

        /** @var Logger $logger */
        $logger = $container->get('logger');
        $logger->debug('DIC: start', ['channel' => 'dic']);

        foreach ($loggerStore as $log) {
            $logger->debug($log, ['channel' => 'dic']);
        }

        $logger->debug(sprintf('DIC: %0.2fms total time', $totalTime * 1000), ['channel' => 'dic']);
    }
}
