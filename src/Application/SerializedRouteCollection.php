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
    private $serializedRoutes;

    /**
     * @var Route[]
     */
    private $cache = [];

    /**
     * @param string[] $routes
     */
    public function __construct(array $routes = [])
    {
        $this->serializedRoutes = $routes;
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

        if (!isset($this->serializedRoutes[$name])) {
            throw new InvalidArgumentException(sprintf('invalid route: %s', $name));
        }

        return $this->cache[$name] = unserialize($this->serializedRoutes[$name]);
    }

    public function all()
    {
        $this->initAll();

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
        return count($this->serializedRoutes);
    }

    /**
     * @param string $name
     * @param Route $route
     */
    public function add($name, Route $route)
    {
        $this->cache[$name] = $route;
    }

    /**
     * @param array|string $name
     */
    public function remove($name)
    {
        unset($name);
        throw new RuntimeException('RoutCollection::remove is not implemented');
    }

    private function initAll()
    {
        return array_map([$this, 'get'], array_keys($this->serializedRoutes));
    }
}
