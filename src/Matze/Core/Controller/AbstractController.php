<?php

namespace Matze\Core\Controller;

use Matze\Core\Authentication\UserVO;
use Matze\Core\Traits\TwigTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

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
		/** @var Session $session */
		$session = $request->getSession();
		$session->getFlashBag()->add($type, $text);
	}

	/**
	 * @param Request $request
	 * @return UserVO|null
	 */
	protected function _getCurrentUser(Request $request) {
		$user = $request->getSession()->get('user');

		if (empty($user)) {
			return new UserVO();
		}

		return $user;
	}
}