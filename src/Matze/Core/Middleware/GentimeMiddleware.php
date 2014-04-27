<?php

namespace Matze\Core\Middleware;

use Matze\Core\Authentication\UserVO;
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
			/** @var UserVO $user */
			$username = $user->getUsername();
		} else {
			$username = '-anonymous-';
		}

		$this->info(sprintf('%0.2fms (route: %s, locale: %s, user:%s)', $diff*1000, $request->getRequestUri(), $session->get('locale'), $username), ['channel' => 'gentime']);
	}
} 