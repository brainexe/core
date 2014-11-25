<?php

namespace BrainExe\Core\DependencyInjection;

use Symfony\Component\DependencyInjection\Container;

/**
 * @todo prepare whitelist -> "lazy loader"
 * @Service(public=false)
 */
class ObjectFinder {

	/**
	 * @var Container
	 */
	private $_container;

	/**
	 * @Inject("@service_container")
	 * @param Container $container
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
