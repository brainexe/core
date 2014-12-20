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
     * @param string $service_id
     * @return mixed
     */
    public function getService($service_id)
    {
        return $this->objectFinder->getService($service_id);
    }
}
