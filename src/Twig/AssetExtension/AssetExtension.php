<?php

namespace Matze\Core\Twig\AssetExtension;

use Twig_Extension;

/**
 * @TwigExtension(compiler=true)
 */
class AssetExtension extends Twig_Extension {

	/**
	 * @var AssetExtensionTokenParser
	 */
	private $_asset_token_parser;

	/**
	 * @Inject("@AssetExtensionTokenParser")
	 * @param AssetExtensionTokenParser $asset_token_parser
	 */
	public function __construct(AssetExtensionTokenParser $asset_token_parser) {
		$this->_asset_token_parser = $asset_token_parser;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTokenParsers() {
		return [
			$this->_asset_token_parser
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return 'asset';
	}
}