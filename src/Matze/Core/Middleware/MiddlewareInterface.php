<?php

namespace Matze\Core\Middleware;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

interface MiddlewareInterface {

	/**
	 * @param Request $request
	 * @param Route $route
	 * @return Response|void $response
	 */
	public function processRequest(Request $request, Route $route);

	/**
	 * @param Request $request
	 * @param Response $response
	 */
	public function processResponse(Request $request, Response $response);

	/**
	 * @param Request $request
	 * @param Response $response
	 * @param Exception $exception
	 */
	public function processException(Request $request, Response $response, Exception $exception);
}