<?php

namespace BrainExe\Core\DependencyInjection;

use Symfony\Component\DependencyInjection\Container;

/**
 * @Service(public=false)
 */
class ObjectFinder
{

    /**
     * @var Container
     */
    private $container;

    /**
     * @Inject("@service_container")
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $serviceId
     * @return mixed
     */
    public function getService($serviceId)
    {
        return $this->container->get($serviceId);
    }
}
