<?php

namespace Matze\Core\Traits;

use Matze\Core\Core;
use PDO;
use Matze\Annotations\Annotations as DI;
use Symfony\Component\DependencyInjection\Container;

trait ServiceContainerTrait {

	/**
	 * @var Container
	 */
	private $_service_container;

	/**
	 * @return Container
	 */
	public function getServiceContainer() {
		return $this->_service_container;
	}

	/**
	 * @todo!
	 * @DI\Inject("service_container")
	 */
	public function setServiceContainer($service_container) {
		$this->_service_container = Core::getServiceContainer();
	}

} 