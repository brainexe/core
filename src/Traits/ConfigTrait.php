<?php

namespace BrainExe\Core\Traits;

use Symfony\Component\DependencyInjection\Container;

trait ConfigTrait
{
    /**
     * @var Container
     */
    private $_container;

    /**
     * @Inject("@service_container")
     * @param Container $container
     */
    public function setContainer(Container $container)
    {
        $this->_container = $container;
    }

    /**
     * @param string $parameter_id
     * @return mixed
     */
    protected function getParameter($parameter_id)
    {
        return $this->_container->getParameter($parameter_id);
    }
}
