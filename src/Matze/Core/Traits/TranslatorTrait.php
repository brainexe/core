<?php

namespace Matze\Core\Traits;

use Symfony\Component\Translation\Translator;

trait TranslatorTrait {

	/**
	 * @var Translator
	 */
	protected $_translator;

	/**
	 * @Inject("@Translator")
	 */
	public function setTranslator(Translator $translator) {
		$this->_translator = $translator;
	}

	/**
	 * @param string      $id         The message id (may also be an object that can be cast to string)
	 * @param array       $parameters An array of parameters for the message
	 * @param string|null $domain     The domain for the message or null to use the default
	 * @param string|null $locale     The locale or null to use the default
	 * @return string The translated string
	 */
	public function trans($id, array $parameters = array(), $domain = null, $locale = null) {
		return $this->_translator->trans($id, $parameters, $domain, $locale);
	}

	/**
	 * @param string      $id         The message id (may also be an object that can be cast to string)
	 * @param integer     $number     The number to use to find the indice of the message
	 * @param array       $parameters An array of parameters for the message
	 * @param string|null $domain     The domain for the message or null to use the default
	 * @param string|null $locale     The locale or null to use the default
	 * @return string The translated string
	 */
	public function transChoice($id, $number, array $parameters = array(), $domain = null, $locale = null) {
		return $this->_translator->transChoice($id, $number, $parameters, $domain, $locale);
	}
}