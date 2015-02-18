<?php

namespace BrainExe\Core\Application;

use ArrayIterator;
use BrainExe\Annotations\Annotations\Service;
use Psr\Log\InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * @Service("Core.RouteCollection", public=false)
 */
class SerializedRouteCollection extends RouteCollection
{
    /**
     * @var string[]
     */
    private $routes;

    /**
     * @var Route[]
     */
    private $cache = [];

    /**
     * @param string[] $routes
     */
    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * @param string $name
     * @return Route
     */
    public function get($name)
    {
        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        if (!isset($this->routes[$name])) {
            throw new InvalidArgumentException(sprintf('invalid route: %s', $name));
        }

        return $this->cache[$name] = unserialize($this->routes[$name]);
    }

    private function init()
    {
        return array_map([$this, 'get'], array_keys($this->routes));
    }

    public function all()
    {
        $this->init();

        return $this->cache;
    }

    /**
     * Gets the current RouteCollection as an Iterator that includes all routes.
     *
     * It implements \IteratorAggregate.
     *
     * @see all()
     *
     * @return ArrayIterator An \ArrayIterator object for iterating over routes
     */
    public function getIterator()
    {
        $this->all();

        return new ArrayIterator($this->cache);
    }

    /**
     * Gets the number of Routes in this collection.
     *
     * @return int The number of routes
     */
    public function count()
    {
        return count($this->routes);
    }

    public function add($name, Route $route)
    {
        unset($name, $route);
        throw new RuntimeException("RoutCollection::add is not implemented");
    }

    public function remove($name)
    {
        unset($name);
        throw new RuntimeException("RoutCollection::remove is not implemented");
    }
}
