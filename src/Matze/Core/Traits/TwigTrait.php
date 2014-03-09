<?php

namespace Matze\Core\Traits;

use Twig_Environment;

trait TwigTrait {

	/**
	 * @var Twig_Environment
	 */
	protected $_twig;

	/**
	 * @Inject("@Twig")
	 */
	public function setTwig(Twig_Environment $twig) {
		$this->_twig = $twig;
	}

	/**
	 * @param string $name
	 * @param array $context
	 * @return string
	 */
	protected function render($name, array $context = []) {
		return $this->_twig->render($name, $context);
	}

}