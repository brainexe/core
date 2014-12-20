<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\Route as RouteAnnotation;
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
        $core_collector = $container->getDefinition('Core.RouteCollection');

        $controllers = $container->findTaggedServiceIds(self::ROUTE_TAG);

        $serialized = [];
        foreach ($controllers as $id => $tag) {
            foreach ($tag as $route_raw) {
                /** @var RouteAnnotation $route */
                $route = $route_raw[0];

                $name = $route->getName() ?: md5($route->getPath());
                if ($route->isCsrf()) {
                    $route->setOptions(['csrf' => true]);
                }

                $serialized[$name] = serialize($this->_createRoute($route));
            }

            $controller = $container->getDefinition($id);
            $controller->clearTag(self::ROUTE_TAG);
        }

        $core_collector->addArgument($serialized);

        $this->_dumpMatcher($container);
    }

    /**
     * @param RouteAnnotation $route
     * @return Route
     */
    private function _createRoute(RouteAnnotation $route)
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
    protected function _dumpMatcher(ContainerBuilder $container)
    {
        if (!is_dir(ROOT . 'cache')) {
            return;
        }

        /** @var RouteCollection $routerCollection */
        $routerCollection = $container->get('Core.RouteCollection');

        $router_file  = sprintf('%scache/router_matcher.php', ROOT);
        $route_dumper = new PhpMatcherDumper($routerCollection);
        $content      = $route_dumper->dump();
        file_put_contents($router_file, $content);

        $router_file  = sprintf('%scache/router_generator.php', ROOT);
        $route_dumper = new PhpGeneratorDumper($routerCollection);
        $content      = $route_dumper->dump();
        file_put_contents($router_file, $content);
    }
}
