<?php

namespace BrainExe\Core\Middleware;

use BrainExe\Core\Authentication\AnonymusUserVO;
use BrainExe\Core\Authentication\DatabaseUserProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

/**
 * @Middleware(priority=8)
 */
class AuthenticationMiddleware extends AbstractMiddleware {

	/**
	 * @var
	 */
	private $_application_guests_allowed;
	/**
	 * @var DatabaseUserProvider
	 */
	private $_database_user_provider;

	/**
	 * @Inject({"%application.guests_allowed%", "@DatabaseUserProvider"})
	 * @param boolean $application_guests_allowed
	 * @param DatabaseUserProvider $database_user_provider
	 */
	public function __construct($application_guests_allowed, DatabaseUserProvider $database_user_provider) {
		$this->_application_guests_allowed = $application_guests_allowed;
		$this->_database_user_provider     = $database_user_provider;
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
		$session   = $request->getSession();
		$user_id   = $session->get('user_id');
		$logged_id = $user_id > 0;

		if ($logged_id) {
			$user = $this->_database_user_provider->loadUserById($user_id);
		} else {
			$user = new AnonymusUserVO();
		}

		$request->attributes->set('user', $user);
		$request->attributes->set('user_id', $user_id);

		if ($this->_application_guests_allowed) {
			return null;
		}

		if ($route->hasDefault('_guest')) {
			return null;
		}

		if (!$logged_id) {
			return new RedirectResponse('#/login');
		}

		return null;
	}
}
