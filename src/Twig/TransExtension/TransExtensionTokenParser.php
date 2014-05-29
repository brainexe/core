<?php

namespace Matze\Core\Twig\TransExtension;

use Twig_Token;
use Twig_TokenParser;

/**
 * @Service(public=false)
 */
class TransExtensionTokenParser extends Twig_TokenParser {

	/**
	 * {@inheritdoc}
	 */
	public function parse(Twig_Token $token) {
		$expr = $this->parser->getExpressionParser()->parseExpression();
		$parameters = [];
		while (!$nodeType = $this->parser->getStream()->test(Twig_Token::BLOCK_END_TYPE)) {
			$parameters[] = $this->parser->getExpressionParser()->parseExpression();
		}
		$this->parser->getStream()->expect(\Twig_Token::BLOCK_END_TYPE);

		return new TransNode($expr, $parameters, $token->getLine());
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTag() {
		return 'trans';
	}
}