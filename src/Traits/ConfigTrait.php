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
     * @param string $parameterId
     * @return mixed
     */
    protected function getParameter($parameterId)
    {
        return $this->container->getParameter($parameterId);
    }
}
