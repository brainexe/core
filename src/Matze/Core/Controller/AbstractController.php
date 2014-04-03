<?php

namespace Matze\Core\Controller;

use Matze\Core\Traits\TwigTrait;
use Symfony\Component\HttpFoundation\Request;
use Twig_Environment;

abstract class AbstractController {

	use TwigTrait;

	const ALERT_WARNING = 'warning';
	const ALERT_INFO = 'info';
	const ALERT_SUCCESS = 'success';
	const ALERT_DANGER = 'danger';

	/**
	 * @param Request $request
	 * @param string $type self::ALERT_*
	 * @param string $text
	 */
	protected function _addFlash(Request $request, $type, $text) {
		$request->getSession()->getFlashBag()->add($type, $text);
	}
}