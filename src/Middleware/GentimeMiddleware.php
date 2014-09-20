<?php

namespace Matze\Core\Middleware;

use Matze\Core\Authentication\UserVO;
use Matze\Core\Traits\LoggerTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

/**
 * @Middleware(priority=1)
 */
class GentimeMiddleware extends AbstractMiddleware {

	use LoggerTrait;

	/**
	 * {@inheritdoc}
	 */
	public function processRequest(Request $request, Route $route, $route_name) {
		if (empty($_SERVER['REQUEST_TIME_FLOAT'])) {
			$_SERVER['REQUEST_TIME_FLOAT'] = microtime(true);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function processResponse(Request $request, Response $response) {
		$start_time = $_SERVER['REQUEST_TIME_FLOAT'];
		$diff = microtime(true) - $start_time;

		$user = $request->attributes->get('user');
		if ($user) {
			/** @var UserVO $user */
			$username = $user->getUsername();
		} else {
			$username = '-anonymous-';
		}

		$this->info(sprintf('%0.2fms (route: %s, user:%s)', $diff*1000, $request->getRequestUri(), $username), ['channel' => 'gentime']);
	}
} 