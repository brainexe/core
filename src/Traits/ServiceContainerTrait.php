<?php

namespace BrainExe\Core\Traits;

use BrainExe\Core\DependencyInjection\ObjectFinder;

/**
 * @deprecated
 */
trait ServiceContainerTrait {

	/**
	 * @var ObjectFinder
	 */
	private $_object_finder_trait;

	/**
	 * @Inject("@ObjectFinder")
	 * @param ObjectFinder $object_finder
	 */
	public function setObjectFinder(ObjectFinder $object_finder) {
		$this->_object_finder_trait = $object_finder;
	}

	/**
	 * @param string $service_id
	 * @return mixed
	 */
	public function getService($service_id) {
		return $this->_object_finder_trait->getService($service_id);
	}

} 
