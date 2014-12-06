<?php

namespace BrainExe\Core\Middleware;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

abstract class AbstractMiddleware implements MiddlewareInterface {

	/**
	 * {@inheritdoc}
	 */
	public function processRequest(Request $request, Route $route, $route_name) {
	}

	/**
	 * {@inheritdoc}
	 */
	public function processResponse(Request $request, Response $response) {
	}

	/**
	 * {@inheritdoc}
	 */
	public function processException(Request $request, Exception $exception) {
	}
}
