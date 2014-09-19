<?php

namespace Matze\Core\EventDispatcher;

use Matze\Core\DependencyInjection\ObjectFinder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class AbstractEventListener implements EventSubscriberInterface {

	/**
	 * @var ObjectFinder
	 */
	private $_object_finder_trait;

	/**
	 * @Inject("@ObjectFinder")
	 * @param ObjectFinder $object_finder
	 */
	public function __construct(ObjectFinder $object_finder) {
		$this->_object_finder_trait = $object_finder;
	}

	/**
	 * @param string $service_id
	 * @return object
	 * @todo protected?
	 */
	public function getService($service_id) {
		return $this->_object_finder_trait->getService($service_id);
	}

} 