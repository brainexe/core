<?php

namespace Tests\BrainExe\Core\Middleware\SessionMiddleware;

use BrainExe\Core\Middleware\SessionMiddleware;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Route;

/**
 * @Covers BrainExe\Core\Middleware\SessionMiddleware
 */
class SessionMiddlewareTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var SessionMiddleware
	 */
	private $_subject;

	/**
	 * @var Session|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockSession;

	public function setUp() {

		$this->_mockSession = $this->getMock(Session::class, [], [], '', false);
		$this->_subject = new SessionMiddleware($this->_mockSession);

	}

	public function testProcessRequest() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$request = new Request();
		$route = new Route();
		$route_name = null;
		$this->_subject->processRequest($request, $route, $route_name);
	}

}
