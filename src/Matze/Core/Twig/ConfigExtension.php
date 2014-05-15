<?php

namespace Matze\Core\Twig;

use Matze\Core\Traits\ConfigTrait;

/**
 * @TwigExtension
 */
class ConfigExtension extends \Twig_Extension {

	use ConfigTrait;

	public function getFunctions() {
		return [
			'config' => new \Twig_Function_Method($this, 'getConfigParameter')
		];
	}

	/**
	 * @param mixed $parameter_id
	 * @return string mixed
	 */
	public function getConfigParameter($parameter_id) {
		return $this->getParameter($parameter_id);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return 'config';
	}
}