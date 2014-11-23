<?php

namespace Tests\BrainExe\Core\Middleware\LocaleMiddleware;

use BrainExe\Core\Middleware\LocaleMiddleware;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\Route;

/**
 * @Covers BrainExe\Core\Middleware\LocaleMiddleware
 */
class LocaleMiddlewareTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var LocaleMiddleware
	 */
	private $_subject;

	public function setUp() {
		$this->_subject = new LocaleMiddleware();
	}

	public function testProcessRequest() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$request = new Request();
		$route = new Route('/route/');
		$session = new Session(new MockArraySessionStorage());
		$request->setSession($session);
		$request->query->set('locate', 'en_EN');


		$route_name = null;
		$this->_subject->processRequest($request, $route, $route_name);
	}

}
