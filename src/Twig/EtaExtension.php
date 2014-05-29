<?php

namespace Matze\Core\Twig;

/**
 * @TwigExtension
 */
class EtaExtension extends \Twig_Extension {

	/**
	 * @var integer
	 */
	private $_now;

	public function __construct() {
		$this->_now = time();
	}

	public function getFilters() {
		return [
			new \Twig_SimpleFilter('eta', [$this, 'getEta'], ['is_safe' => ['all']])
		];
	}

	/**
	 * @param integer $timestamp
	 * @return string
	 */
	public function getEta($timestamp) {
		return sprintf('<span class="eta" data-timestamp="%s"></span>', $timestamp);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return 'eta';
	}
}