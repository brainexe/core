<?php

namespace Matze\Core\Middleware;

use Matze\Core\Traits\CacheTrait;
use Matze\Core\Traits\LoggerTrait;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\Route;

/**
 * @Middleware(priority=12)
 */
class AuthenticationMiddleware extends AbstractMiddleware {

	use LoggerTrait;

	/**
	 * {@inheritdoc}
	 */
	public function processResponse(Request $request, Response $response) {
	}

	/**
	 * {@inheritdoc}
	 */
	public function processRequest(Request $request, Route $route, $route_name) {
		if (strpos($route_name, 'authenticate.') === 0) {
			return null;
		}

		$session = $request->getSession();
		$user = $session ? $session->get('user') : null;
		$logged_id = $user && $user->id > 0;

		if (!$logged_id) {
			return new RedirectResponse('/login/');
		}
	}
} 