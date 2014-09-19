<?php

namespace Matze\Core\Twig\AssetExtension;

use Matze\Core\Assets\AssetUrl;
use Twig_Token;
use Twig_TokenParser;

/**
 * @Service(public=false)
 */
class AssetExtensionTokenParser extends Twig_TokenParser {

	/**
	 * @var AssetUrl
	 */
	private $_asset_url;

	/**
	 * @Inject("@AssetUrl")
	 * @param AssetUrl $asset_url
	 */
	public function __construct(AssetUrl $asset_url) {
		$this->_asset_url = $asset_url;
	}

	/**
	 * {@inheritdoc}
	 */
	public function parse(Twig_Token $token) {
		echo "..";

		$expr = $this->parser->getExpressionParser()->parseExpression();

		$this->parser->getStream()->expect(\Twig_Token::BLOCK_END_TYPE);

		$config_name = $expr->getAttribute('value');

		$url = $this->_asset_url->getAssetUrl($config_name);

		return new \Twig_Node_Text($url, $token->getLine());
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTag() {
		echo "..";
		return 'asset_url';
	}
}