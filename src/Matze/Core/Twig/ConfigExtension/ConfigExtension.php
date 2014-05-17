<?php

namespace Matze\Core\Twig\ConfigExtension;

use Matze\Core\Traits\ConfigTrait;
use Matze\Core\Twig\UrlExtension\ConfigExtensionTokenParser;

/**
 * @TwigExtension(compiler=true)
 */
class ConfigExtension extends \Twig_Extension {

	/**
	 * @var ConfigExtensionTokenParser
	 */
	private $_config_token_parser;

	/**
	 * @Inject("@ConfigExtensionTokenParser")
	 */
	public function __construct(ConfigExtensionTokenParser $config_token_parser) {
		$this->_config_token_parser = $config_token_parser;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTokenParsers() {
		return [
			$this->_config_token_parser
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return 'config';
	}
}