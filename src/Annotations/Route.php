<?php

namespace BrainExe\Core\Annotations;

use Doctrine\Common\Annotations\Annotation;
use Symfony\Component\Routing\Annotation\Route as SymfonyRoute;

/**
 * @Annotation
 */
class Route extends SymfonyRoute {

	/**
	 * @var boolean
	 */
	private $csrf = false;

	/**
	 * @return boolean
	 */
	public function isCsrf() {
		return $this->csrf;
	}

	/**
	 * @param boolean $csrf
	 */
	public function setCsrf($csrf) {
		$this->csrf = $csrf;
	}

}