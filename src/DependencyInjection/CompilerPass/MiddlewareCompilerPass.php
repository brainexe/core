<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass;
use BrainExe\Core\Middleware\AbstractMiddleware;
use BrainExe\Core\Middleware\MiddlewareInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @CompilerPass
 */
class MiddlewareCompilerPass implements CompilerPassInterface
{

    const TAG = 'middleware';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $appKernel   = $container->getDefinition('AppKernel');
        $middlewares = $container->getParameter('application.middlewares');
        $references  = [];

        foreach ($middlewares as $serviceId) {
            $references[] = new Reference($serviceId);
        }
        $appKernel->addMethodCall('setMiddlewares', [$references]);
        $container->setParameter('application.middlewares', []);
    }
}
