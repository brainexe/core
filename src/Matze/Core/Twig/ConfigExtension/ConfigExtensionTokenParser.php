<?php

namespace Matze\Core\Twig\UrlExtension;

use Matze\Core\Traits\ConfigTrait;
use Twig_Token;
use Twig_TokenParser;

/**
 * @Service("ConfigExtensionTokenParser", public=false)
 */
class ConfigExtensionTokenParser extends Twig_TokenParser {

	use ConfigTrait;

	/**
	 * {@inheritdoc}
	 */
	public function parse(Twig_Token $token) {
		$expr = $this->parser->getExpressionParser()->parseExpression();

		$this->parser->getStream()->expect(\Twig_Token::BLOCK_END_TYPE);

		$config_name = $expr->getAttribute('value');
		$config_value = $this->getParameter($config_name);

		return new \Twig_Node_Text($config_value, $token->getLine());
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTag() {
		return 'config';
	}
}