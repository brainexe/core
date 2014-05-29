<?php

namespace Matze\Core\Twig\PiwikExtension;

use Twig_Extension;

/**
 * @TwigExtension(compiler=true)
 */
class PiwikExtension extends Twig_Extension {

	/**
	 * @var PiwikExtensionTokenParser
	 */
	private $piwik_extension_token_parser;

	/**
	 * @Inject("@PiwikExtensionTokenParser")
	 */
	public function __construct(PiwikExtensionTokenParser $piwik_extension_token_parser) {
		$this->piwik_extension_token_parser = $piwik_extension_token_parser;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTokenParsers() {
		return [
			$this->piwik_extension_token_parser
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return 'piwik';
	}

}
