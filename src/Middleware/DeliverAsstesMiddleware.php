<?php

namespace Matze\Core\Middleware;

use Exception;
use Matze\Core\Traits\LoggerTrait;
use Matze\Core\Traits\ServiceContainerTrait;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeExtensionGuesser;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Route;

/**
 * @todo debug only
 * @Middleware(priority=3)
 */
class DeliverAsstesMiddleware extends AbstractMiddleware {

	use LoggerTrait;

	/**
	 * {@inheritdoc}
	 */
	public function processException(Request $request, Response $response, Exception $exception) {
		if (!$exception instanceof ResourceNotFoundException) {
			return;
		}

		$request_uri = $request->getRequestUri();

		if (preg_match('/\.\w{2,4}$/', $request_uri)) {
			// TODO check for ..
			$full_path = ROOT . 'web' . $request_uri;

			if (file_exists($full_path)) {

				$mime_type_guess = MimeTypeGuesser::getInstance();
				$mimetype = $mime_type_guess->guess($full_path);

				$this->error($mimetype . $full_path);

				$response = new Response(file_get_contents($full_path));
				$request->headers->set('Content-Type', $mimetype);

				return $response;
			}
		}
	}
}