<?php

namespace Matze\Core\Twig\UrlExtension;

use Symfony\Component\Routing\Generator\UrlGenerator;
use Twig_Extension;

/**
 * @TODO compile only
 * @TwigExtension
 */
class UrlExtension extends Twig_Extension {

	/**
	 * @var UrlGenerator
	 */
	private $_url_generator;

	/**
	 * @Inject("@UrlGenerator")
	 */
	public function __construct(UrlGenerator $url_generator) {
		$this->_url_generator = $url_generator;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTokenParsers() {
		return [
			new UrlExtensionTokenParser($this->_url_generator)
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return 'url';
	}
}