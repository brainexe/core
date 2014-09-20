<?php

namespace Matze\Core\Middleware;

use Exception;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * @todo finalize
 * @ # Middleware(priority=20)
 */
class DeliverAsstesMiddleware extends AbstractMiddleware {

	/**
	 * {@inheritdoc}
	 */
	public function processException(Request $request, Exception $exception) {
		if (!$exception instanceof ResourceNotFoundException) {
			return null;
		}

		$request_uri = $request->getRequestUri();

		if (preg_match('/\.\w{2,4}$/', $request_uri)) {
			// TODO check for ..
			$full_path = ROOT . 'web' . $request_uri;

			if (file_exists($full_path)) {
				$mime_type_guess = MimeTypeGuesser::getInstance();
				$mimetype = $mime_type_guess->guess($full_path);

				list(,$extension) = explode('.', $full_path);
				switch ($extension) {
					case 'js':
						$mimetype = 'application/x-javascript';
						break;
					case 'html':
						$mimetype = 'text/html';
						break;
					case 'css':
						$mimetype = 'text/css';
						break;
				}

				$response = new Response(file_get_contents($full_path));
				$response->headers->set('Content-Type', $mimetype);
				$response->setCache([
					'public' => true,
					'max_age' => 3600
				]);

				return $response;
			}
		}

		return null;
	}
}