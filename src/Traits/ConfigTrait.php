<?php

namespace BrainExe\Core\Traits;

use BrainExe\Core\Annotations\Inject;
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
     * @Inject("=container.getParameterBag()")
     * @param ParameterBag $parameterBag
     */
    public function setParameterBag(ParameterBag $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    /**
     * @param string $parameterId
     * @return mixed
     */
    protected function getParameter(string $parameterId)
    {
        return $this->parameterBag->get($parameterId);
    }
}
