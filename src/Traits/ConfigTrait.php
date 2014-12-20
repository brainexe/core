<?php

namespace BrainExe\Core\Traits;

use Symfony\Component\DependencyInjection\Container;

trait ConfigTrait
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @Inject("@service_container")
     * @param Container $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $parameter_id
     * @return mixed
     */
    protected function getParameter($parameter_id)
    {
        return $this->container->getParameter($parameter_id);
    }
}
