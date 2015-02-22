<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Twig_Extension_Debug;
use Twig_Loader_Array;

/**
 * @CompilerPass
 */
class TwigExtensionCompilerPass implements CompilerPassInterface
{

    const TAG = 'twig_extension';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        /** @var Definition $twig */
        /** @var Definition $twigCompiler */
        $twig = $container->getDefinition('Twig');
        $twigCompiler = $container->getDefinition('TwigCompiler');

        if (CORE_STANDALONE) {
            $twig->setArguments([new Definition(Twig_Loader_Array::class, [[]])]);
        }

        $services = $container->findTaggedServiceIds(self::TAG);

        $debug = $container->getParameter('debug');
        foreach ($services as $serviceId => $tag) {
            $service = $container->getDefinition($serviceId);
            $service->setPublic(false);

            if ($tag[0]['compiler']) {
                $twigCompiler->addMethodCall('addExtension', [new Reference($serviceId)]);
            } else {
                $twig->addMethodCall('addExtension', [new Reference($serviceId)]);
            }
        }

        if ($debug) {
            $twig->addMethodCall('addExtension', [new Definition(Twig_Extension_Debug::class)]);
            $twig->addMethodCall('enableStrictVariables');
        }
    }
}
