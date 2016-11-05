<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @CompilerPass
 */
class Log implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('logger');

        $logLevels = [
            'error.log' => Logger::ERROR,
            'info.log' => Logger::INFO,
            'info2.log' => Logger::INFO,
        ];

        if ($container->getParameter('debug')) {
            $logLevels['debug.log'] = Logger::DEBUG;
        }

        $baseDir = $container->getParameter('logger.dir');
        foreach ($logLevels as $file => $level) {
            $definition->addMethodCall('pushHandler', [
                new Definition(StreamHandler::class, [
                    $baseDir . $file,
                    $level
                ])
            ]);
        }
    }
}
