<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass;
use BrainExe\Core\Logger\ChannelStreamHandler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

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
        $logger = $container->getDefinition('logger');

        foreach ($container->getParameter('logger.channels') as $config) {
            $logger->addMethodCall('pushHandler', [
                new Definition(ChannelStreamHandler::class, $config)
            ]);
        }

        /** @var ParameterBag $parameterBag */
        $parameterBag = $container->getParameterBag();
        $parameterBag->remove('logger.channels');
    }
}
