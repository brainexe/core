<?php

namespace Matze\Core\Twig\TransExtension;

use Twig_Extension;

/**
 * @TwigExtension
 */
class TransExtension extends Twig_Extension {

	/**
	 * @var TransExtensionTokenParser
	 */
	private $trans_extension_token_parser;

	/**
	 * @Inject("@TransExtensionTokenParser")
	 */
	public function __construct(TransExtensionTokenParser $trans_extension_token_parser) {
		$this->trans_extension_token_parser = $trans_extension_token_parser;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTokenParsers() {
		return [
			$this->trans_extension_token_parser
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return 'trans';
	}
}