<?php

namespace Matze\Core\Middleware;

use Matze\Core\Traits\IdGeneratorTrait;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Route;

/**
 * @Middleware(priority=4)
 */
class CsrfMiddleware extends AbstractMiddleware {

	const CSRF = 'csrf';

	use IdGeneratorTrait;

	/**
	 * @var string
	 */
	private $_new_token = null;

	/**
	 * {@inheritdoc}
	 */
	public function processRequest(Request $request, Route $route, $route_name) {
		$given_token = $request->cookies->get(self::CSRF);

		if (empty($given_token)) {
			$this->_renewCsrfToken();
		}

		if (!$request->isMethod('POST') && !$route->hasOption(self::CSRF)) {
			return;
		}

		$expected_token = $request->getSession()->get(self::CSRF);

		$this->_renewCsrfToken();

		if (empty($given_token) || $given_token !== $expected_token) {
			$this->_renewCsrfToken();

			throw new MethodNotAllowedException(['POST'], "invalid CSRF token");
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function processResponse(Request $request, Response $response) {
		if ($this->_new_token) {
			$request->getSession()->set(self::CSRF, $this->_new_token);
			$response->headers->setCookie(new Cookie(self::CSRF, $this->_new_token));
			$this->_new_token = null;
		}
	}

	/**
	 * @return void
	 */
	private function _renewCsrfToken() {
		$this->_new_token = $this->generateRandomId();
	}

}