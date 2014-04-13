<?php

namespace Matze\Core\Middleware;

use Matze\Core\Traits\LoggerTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * @Middleware(priority=1)
 */
class GentimeMiddleware extends AbstractMiddleware {

	use LoggerTrait;

	/**
	 * {@inheritdoc}
	 */
	public function processResponse(Request $request, Response $response) {
		$start_time = $_SERVER['REQUEST_TIME_FLOAT'];
		$diff = microtime(true) - $start_time;

		$session = $request->getSession() ?: new Session(new MockArraySessionStorage());
		if ($session && $user = $session->get('user')) {
			$username = $user->getUsername();
		} else {
			$username = '-anonymous-';
		}

		$this->info(sprintf('Response time: %0.2fms (route: %s, locale: %s, user:%s)', $diff*1000, $request->attributes->get('_controller'), $session->get('locale'), $username));
	}
} 