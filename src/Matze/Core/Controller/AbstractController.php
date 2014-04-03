<?php

namespace Matze\Core\Controller;

use Symfony\Component\HttpFoundation\Request;
use Twig_Environment;

abstract class AbstractController {

	const ALERT_WARNING = 'warning';
	const ALERT_INFO = 'info';
	const ALERT_SUCCESS = 'success';
	const ALERT_DANGER = 'danger';

	/**
	 * @var Twig_Environment
	 */
	protected $_twig;

	/**
	 * @Inject("@Twig")
	 */
	public function __construct(Twig_Environment $twig) {
		$this->_twig = $twig;
	}

	/**
	 * @param string $name
	 * @param array $context
	 * @return string
	 */
	protected function render($name, array $context = []) {
		return $this->_twig->render($name, $context);
	}

	/**
	 * @param Request $request
	 * @param string $type self::ALERT_*
	 * @param string $text
	 */
	protected function _addFlash(Request $request, $type, $text) {
		$request->getSession()->getFlashBag()->add($type, $text);
	}
}