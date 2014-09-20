<?php

namespace Matze\Core\Middleware;

use Exception;
use Matze\Core\Application\ErrorView;
use Matze\Core\Application\UserException;
use Matze\Core\Controller\AbstractController;
use Matze\Core\Traits\ServiceContainerTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Route;

/**
 * @todo debug only
 * @Middleware
 */
class DeliverAsstesMiddleware extends AbstractMiddleware {


	/**
	 * {@inheritdoc}
	 */
	public function processRequest(Request $request, Route $route, $route_name) {
		$request_uri = $request->getRequestUri();

		if (preg_match('/\.\w{2,4}$/', $request_uri)) {
			// TODO check for ..
			$full_path = ROOT . '/web' . $request_uri;
			if (file_exists($full_path)) {
				
			}
		}
		list(,$extension) = explode('.', $request_uri);
		if ()

		print_r($request);
	}
}