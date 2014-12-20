<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\Route as RouteAnnotation;
use BrainExe\Core\Application\SerializedRouteCollection;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Routing\Generator\Dumper\PhpGeneratorDumper;
use Symfony\Component\Routing\Matcher\Dumper\PhpMatcherDumper;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * @CompilerPass
 */
class ControllerCompilerPass implements CompilerPassInterface
{

    const CONTROLLER_TAG = 'controller';
    const ROUTE_TAG = 'route';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $coreCollector = $container->getDefinition('Core.RouteCollection');
        $controllers   = $container->findTaggedServiceIds(self::ROUTE_TAG);

        $serialized = [];
        foreach ($controllers as $controllerId => $tag) {
            foreach ($tag as $routeRaw) {
                /** @var RouteAnnotation $route */
                $route = $routeRaw[0];

                $name = $route->getName() ?: md5($route->getPath());
                if ($route->isCsrf()) {
                    $route->setOptions(['csrf' => true]);
                }

                $serialized[$name] = serialize($this->createRoute($route));
            }

            $controller = $container->getDefinition($controllerId);
            $controller->clearTag(self::ROUTE_TAG);
        }

        $coreCollector->addArgument($serialized);

        $this->dumpMatcher($container);
    }

    /**
     * @param RouteAnnotation $route
     * @return Route
     */
    private function createRoute(RouteAnnotation $route)
    {
        if ($route->isCsrf()) {
            $route->setOptions(['csrf' => true]);
        }

        return new Route(
            $route->getPath(),
            $route->getDefaults(),
            $route->getRequirements(),
            $route->getOptions(),
            $route->getHost(),
            $route->getSchemes(),
            $route->getMethods(),
            $route->getCondition()
        );
    }

    /**
     * @param ContainerBuilder $container
     * @codeCoverageIgnore
     */
    protected function dumpMatcher(ContainerBuilder $container)
    {
        if (!is_dir(ROOT . 'cache')) {
            return;
        }

        /** @var SerializedRouteCollection $routerCollection */
        $routerCollection = $container->get('Core.RouteCollection');

        $routerFile  = sprintf('%scache/router_matcher.php', ROOT);
        $routeDumper = new PhpMatcherDumper($routerCollection);
        $content     = $routeDumper->dump();
        file_put_contents($routerFile, $content);

        $routerFile  = sprintf('%scache/router_generator.php', ROOT);
        $routeDumper = new PhpGeneratorDumper($routerCollection);
        $content     = $routeDumper->dump();
        file_put_contents($routerFile, $content);
    }
}
