<?php

namespace Tests\BrainExe\Core\Middleware\AuthenticationMiddleware;

use BrainExe\Core\Authentication\DatabaseUserProvider;
use BrainExe\Core\Middleware\AuthenticationMiddleware;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

/**
 * @Covers BrainExe\Core\Middleware\AuthenticationMiddleware
 */
class AuthenticationMiddlewareTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var AuthenticationMiddleware
	 */
	private $_subject;

	/**
	 * @var DatabaseUserProvider|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockDatabaseUserProvider;

	public function setUp() {
		$this->_mockDatabaseUserProvider = $this->getMock(DatabaseUserProvider::class, [], [], '', false);

		$this->_subject = new AuthenticationMiddleware(false, $this->_mockDatabaseUserProvider);
	}

	public function testProcessResponse() {
		$request = new Request();
		$response = new Response();
		$this->_subject->processResponse($request, $response);
	}

	public function testProcessRequest() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$request = new Request();
		$route = new Route();
		$route_name = null;
		$this->_subject->processRequest($request, $route, $route_name);
	}

}
