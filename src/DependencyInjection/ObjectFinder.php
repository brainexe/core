<?php

namespace Matze\Core\DependencyInjection;

use Symfony\Component\DependencyInjection\Container;

/**
 * @Service(public=false)
 */
class ObjectFinder {

	/**
	 * @var Container
	 */
	private $_container;

	/**
	 * @Inject("@service_container")
	 */
	public function __construct(Container $container) {
		$this->_container = $container;
	}

	/**
	 * @param string $service_id
	 * @return mixed
	 */
	public function getService($service_id) {
		return $this->_container->get($service_id);
	}

} 
