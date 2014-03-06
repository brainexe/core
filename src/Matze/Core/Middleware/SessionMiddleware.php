<?php

namespace Matze\Core\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Route;

/**
 * @Middleware(priority=10)
 */
class SessionMiddleware extends AbstractMiddleware {

	/**
	 * @var Session
	 */
	private $_redis_session;

	/**
	 * @Inject({"@RedisSession"})
	 */
	public function __construct(Session $redis_session) {
		$this->_redis_session = $redis_session;
	}

	/**
	 * {@inheritdoc}
	 */
	public function processRequest(Request $request, Route $route) {
		$request->setSession($this->_redis_session);
	}
} 