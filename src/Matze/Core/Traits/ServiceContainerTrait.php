<?php

namespace Matze\Core\Traits;

use Matze\Annotations\Annotations as DI;
use Matze\Core\DependencyInjection\ObjectFinder;

trait ServiceContainerTrait {

	/**
	 * @var ObjectFinder
	 */
	private $_object_finder_trait;

	/**
	 * @Inject("@ObjectFinder")
	 */
	public function setObjectFinder(ObjectFinder $object_finder) {
		$this->_object_finder_trait = $object_finder;
	}

	/**
	 * @param string $service_id
	 */
	protected function getService($service_id) {
		return $this->_object_finder_trait->getService($service_id);
	}

} 