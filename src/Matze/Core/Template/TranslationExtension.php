<?php

namespace Matze\Core\Template;

use Matze\Core\Template\Translation\TransTokenParser;
use Matze\Core\Traits\TranslatorTrait;
use Symfony\Component\Translation\Translator;

/**
 * @TwigExtension
 */
class TranslationExtension extends \Twig_Extension {

	use TranslatorTrait;

	/**
	 * @return Translator
	 */
	public function getTranslator() {
		return $this->_translator;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFilters() {
		return [
			new \Twig_SimpleFilter('trans', [$this, 'trans']),
			new \Twig_SimpleFilter('transchoice', [$this, 'transchoice'])
		];
	}

	/**
	 * Returns the token parser instance to add to the existing list.
	 *
	 * @return array An array of Twig_TokenParser instances
	 */
	public function getTokenParsers() {
		return [
			// {% trans %}Symfony is great!{% endtrans %}
			new TransTokenParser(),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return 'translator';
	}
}