<?php

namespace BrainExe\Core\Traits;

use BrainExe\Annotations\Annotations\Inject;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * @api
 */
trait ConfigTrait
{
    /**
     * @var ParameterBag
     */
    private $parameterBag;

    /**
     * @Inject("@Service_container")
     * @param Container $container
     */
    public function setContainer(Container $container)
    {
        $this->parameterBag = $container->getParameterBag();
    }

    /**
     * @param string $parameterId
     * @return mixed
     */
    protected function getParameter($parameterId)
    {
        return $this->parameterBag->get($parameterId);
    }
}
