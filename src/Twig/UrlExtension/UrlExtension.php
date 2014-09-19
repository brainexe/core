<?php

namespace Matze\Core\Twig\UrlExtension;

use Symfony\Component\Routing\Generator\UrlGenerator;
use Twig_Extension;

/**
 * @TwigExtension(compiler=true)
 */
class UrlExtension extends Twig_Extension {

	/**
	 * @var UrlGenerator
	 */
	private $_url_generator;

	/**
	 * @Inject("@UrlGenerator")
	 * @param UrlGenerator $url_generator
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