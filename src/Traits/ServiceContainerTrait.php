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
    private $_objectFinder;

    /**
     * @Inject("@ObjectFinder")
     * @param ObjectFinder $object_finder
     */
    public function setObjectFinder(ObjectFinder $object_finder)
    {
        $this->_objectFinder = $object_finder;
    }

    /**
     * @param string $service_id
     * @return mixed
     */
    public function getService($service_id)
    {
        return $this->_objectFinder->getService($service_id);
    }
}
