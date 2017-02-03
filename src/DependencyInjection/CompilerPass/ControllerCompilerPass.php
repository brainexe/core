<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass;
use BrainExe\Core\Annotations\Route as RouteAnnotation;
use BrainExe\Core\Application\SerializedRouteCollection;
use BrainExe\Core\Traits\FileCacheTrait;
use Exception;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\Matcher\Dumper\PhpMatcherDumper;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * @CompilerPass(priority=5)
 */
class ControllerCompilerPass implements CompilerPassInterface
{
    use FileCacheTrait;

    const CONTROLLER_TAG = 'controller';
    const ROUTE_TAG      = 'route';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $controllers = $container->findTaggedServiceIds(self::ROUTE_TAG);

        $serialized = [];
        foreach ($controllers as $controllerId => $tag) {
            foreach ($tag as $routeRaw) {
                /** @var RouteAnnotation $route */
                $route = $routeRaw[0];

                $name = $route->getName();
                if (empty($name)) {
                    throw new Exception(sprintf('"name" is missing for @Route(%s)', $controllerId));
                } elseif (isset($serialized[$name])) {
                    throw new Exception(sprintf('Route name %s does already exits in %s', $name, $controllerId));
                }

                $serialized[$name] = serialize($this->createRoute($route));
            }

            $controller = $container->getDefinition($controllerId);
            $controller->clearTag(self::ROUTE_TAG);
        }

        $this->dumpMatcher($container, $serialized);
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
     * @param array $routes
     * @codeCoverageIgnore
     */
    protected function dumpMatcher(ContainerBuilder $container, array $routes)
    {
        ksort($routes);

        $this->dumpVariableToCache(SerializedRouteCollection::CACHE_FILE, $routes);

        /** @var SerializedRouteCollection $routerCollection */
        $routerCollection = $container->get('Core.RouteCollection');

        $routerFile  = sprintf('%scache/router_matcher.php', ROOT);
        $routeDumper = new PhpMatcherDumper($routerCollection);
        $content     = $routeDumper->dump();
        file_put_contents($routerFile, $content);
        @chmod($routerFile, 0777);
    }
}
