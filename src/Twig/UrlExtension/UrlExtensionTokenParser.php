<?php

namespace Matze\Core\Twig\UrlExtension;

use Symfony\Component\Routing\Generator\UrlGenerator;
use Twig_Token;
use Twig_TokenParser;

class UrlExtensionTokenParser extends Twig_TokenParser {

	/**
	 * @var UrlGenerator
	 */
	private $_url_generator;

	/**
	 * @param UrlGenerator $url_generator
	 */
	public function __construct(UrlGenerator $url_generator) {
		$this->_url_generator = $url_generator;
	}

	/**
	 * {@inheritdoc}
	 */
	public function parse(Twig_Token $token) {
		$expr = $this->parser->getExpressionParser()->parseExpression();

		$this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);

		$url_name = $expr->getAttribute('value');
		$url = $this->_url_generator->generate($url_name);

		return new \Twig_Node_Text($url, $token->getLine());
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTag() {
		return 'url';
	}
}