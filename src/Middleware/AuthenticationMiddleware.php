<?php

namespace Matze\Core\Middleware;

use Matze\Core\Traits\LoggerTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

/**
 * @Middleware(priority=8)
 */
class AuthenticationMiddleware extends AbstractMiddleware {

	use LoggerTrait;

	/**
	 * @var
	 */
	private $_application_guests_allowed;

	/**
	 * @Inject("%application.guests_allowed%")
	 */
	public function __construct($application_guests_allowed) {
		$this->_application_guests_allowed = $application_guests_allowed;
	}

	/**
	 * {@inheritdoc}
	 */
	public function processResponse(Request $request, Response $response) {
	}

	/**
	 * {@inheritdoc}
	 */
	public function processRequest(Request $request, Route $route, $route_name) {
		if ($this->_application_guests_allowed) {
			return null;
		}

		if (strpos($route_name, 'authenticate.') === 0) {
			return null;
		}
		if ($route_name === 'index') {
			return null;
		}

		$session = $request->getSession();
		$user = $session ? $session->get('user') : null;
		$logged_id = $user && $user->id > 0;

		if (!$logged_id) {
			return new RedirectResponse('#/login');
		}

		return null;
	}
} 