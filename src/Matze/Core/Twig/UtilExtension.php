<?php

namespace Matze\Core\Twig;

/**
 * @TwigExtension
 */
class UtilExtension extends \Twig_Extension {

	public function getFunctions() {
		return [
			'print_r' => new \Twig_Function_Method($this, 'print_r', ['is_safe' => ['all']])
		];
	}

	/**
	 * @param mixed $variable
	 * @return string mixed
	 */
	public function print_r($variable) {
		return nl2br(print_r($variable, true));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return 'util';
	}
}