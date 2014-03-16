<?php

namespace Matze\Core\Twig\TransExtension;

use Twig_Extension;

/**
 * @TwigExtension
 */
class TransExtension extends Twig_Extension {

	/**
	 * {@inheritdoc}
	 */
	public function getTokenParsers() {
		return [
			new TransExtensionTokenParser()
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return 'trans';
	}
}