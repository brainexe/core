<?php

namespace Matze\Core\Middleware;

use Matze\Core\Traits\LoggerTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Middleware(priority=1)
 */
class DebugMiddleware extends AbstractMiddleware {

	use LoggerTrait;

	/**
	 * {@inheritdoc}
	 */
	public function processResponse(Request $request, Response $response) {
		$start_time = $_SERVER['REQUEST_TIME_FLOAT'];
		$diff = microtime(true) - $start_time;

		if ($user = $request->getSession()->get('user')) {
			$username = $user->getUsername();
		} else {
			$username = '-anonymous-';
		}

		$this->debug(sprintf('Response time: %0.2fms (%s) (%s)', $diff*1000, $request->attributes->get('_controller'), $username));
	}
} 