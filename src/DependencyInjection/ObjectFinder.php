<?php

namespace BrainExe\Core\DependencyInjection;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use Symfony\Component\DependencyInjection\Container;

/**
 * @Service(public=false)
 * @api
 */
class ObjectFinder
{

    /**
     * @var Container
     */
    private $container;

    /**
     * @Inject("@Service_container")
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
