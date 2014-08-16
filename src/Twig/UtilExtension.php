<?php

namespace Matze\Core\Twig;

use Twig_SimpleFilter;

/**
 * @TwigExtension
 */
class UtilExtension extends \Twig_Extension {

	/**
	 * {@inheritdoc}
	 */
	public function getFunctions() {
		return [
			'print_r' => new \Twig_Function_Method($this, 'print_r', ['is_safe' => ['all']])
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFilters() {
		return [
			new Twig_SimpleFilter('sum', 'array_sum', ['is_safe' => ['all']]),
			new Twig_SimpleFilter('json', 'json_encode', ['is_safe' => ['all']]),
		];
	}

	/**
	 * @param mixed $variable
	 * @return string mixed
	 */
	public function print_r($variable) {
		return print_r($variable, true);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return 'util';
	}
}