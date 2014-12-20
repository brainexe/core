<?php

namespace BrainExe\Core\Traits;

use BrainExe\Core\DependencyInjection\ObjectFinder;

/**
 * @deprecated
 */
trait ServiceContainerTrait
{

    /**
     * @var ObjectFinder
     */
    private $objectFinder;

    /**
     * @Inject("@ObjectFinder")
     * @param ObjectFinder $objectFinder
     */
    public function setObjectFinder(ObjectFinder $objectFinder)
    {
        $this->objectFinder = $objectFinder;
    }

    /**
     * @param string $serviceId
     * @return mixed
     */
    public function getService($serviceId)
    {
        return $this->objectFinder->getService($serviceId);
    }
}
