<?php

namespace BrainExe\Core\Traits;

use BrainExe\Annotations\Annotations\Inject;
use Symfony\Component\DependencyInjection\Container;

/**
 * @api
 */
trait ConfigTrait
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @Inject("@Service_container")
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
