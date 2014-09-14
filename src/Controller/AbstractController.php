<?php

namespace Matze\Core\Controller;

use Matze\Core\Authentication\AnonymusUserVO;
use Matze\Core\Authentication\UserVO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractController {

	const ALERT_WARNING = 'warning';
	const ALERT_INFO = 'info';
	const ALERT_SUCCESS = 'success';
	const ALERT_DANGER = 'danger';

	/**
	 * @param Response $response
	 * @param string $type self::ALERT_*
	 * @param string $text
	 * @todo put to response as X-Header
	 */
	protected function _addFlash(Response $response, $type, $text) {
		$response->headers->set('X-Flash', json_encode([$type, $text]));
	}

	/**
	 * @param Request $request
	 * @return UserVO|null
	 */
	protected function _getCurrentUser(Request $request) {
		$user = $request->attributes->get('user');

		if (empty($user)) {
			return new AnonymusUserVO();
		}

		return $user;
	}
}