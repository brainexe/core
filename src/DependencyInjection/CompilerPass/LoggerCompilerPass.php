<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass;
use Monolog\Handler\ChromePHPHandler;
use Monolog\Handler\HipChatHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @CompilerPass
 */
class LoggerCompilerPass implements CompilerPassInterface
{

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $logger = $container->getDefinition('monolog.Logger');

        if ($container->getParameter('core_standalone')) {
             // we have to remove all handlers...
            $logger->removeMethodCall('pushHandler');
            $logger->removeMethodCall('pushHandler');

             // ...and add the TestHandler
            $logger->addMethodCall('pushHandler', [new Definition(TestHandler::class)]);
        }

        if ($container->getParameter('debug')) {
            $logger->addMethodCall('pushHandler', [new Definition(ChromePHPHandler::class)]);
            $logger->addMethodCall('pushHandler', [
                new Definition(StreamHandler::class, ['php://stdout', Logger::INFO])
            ]);
        }

        if ($container->getParameter('hipchat.api_token')) {
            $logger->addMethodCall('pushHandler', [new Definition(HipChatHandler::class, [
                $container->getParameter('hipchat.api_token'),
                $container->getParameter('hipchat.room'),
                $container->getParameter('hipchat.name'),
                false,
                $container->getParameter('hipchat.logLevel'),
            ])]);
        }
    }
}
