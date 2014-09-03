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
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFilters() {
		return [
			new Twig_SimpleFilter('json', 'json_encode', ['is_safe' => ['all']]),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return 'util';
	}
}