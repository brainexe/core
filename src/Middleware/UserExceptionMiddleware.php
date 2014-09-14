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

/**
 * @Middleware
 */
class UserExceptionMiddleware extends AbstractMiddleware {

	use ServiceContainerTrait;

	/**
	 * @param Request $request
	 * @param Response $response
	 * @param Exception $exception
	 */
	public function processException(Request $request, Response $response, Exception $exception) {
		if ($exception instanceof ResourceNotFoundException) {
			$exception = new UserException(sprintf('Page not found: %s', $request->getRequestUri()), 0, $exception);
			$response->setStatusCode(404);
		} elseif ($exception instanceof MethodNotAllowedException) {
			$exception = new UserException('You are not allowed to access the page', 0, $exception);
			$response->setStatusCode(405);
		}

		if ($request->isXmlHttpRequest()) {
			$message = $exception->getMessage() ?: 'An internal error occured';
			$response->headers->set('X-Flash', json_encode([AbstractController::ALERT_DANGER, $message]));
			$response->setStatusCode(500);

		} else {
			/** @var ErrorView $error_view */
			$error_view = $this->getService('ErrorView');
			$response_string = $error_view->renderException($request, $exception);
			$response->setContent($response_string);
		}

	}
}