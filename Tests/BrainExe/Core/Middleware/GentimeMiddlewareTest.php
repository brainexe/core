<?php

namespace Tests\BrainExe\Core\Middleware\GentimeMiddleware;

use BrainExe\Core\Middleware\GentimeMiddleware;
use Monolog\Logger;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

/**
 * @Covers BrainExe\Core\Middleware\GentimeMiddleware
 */
class GentimeMiddlewareTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var GentimeMiddleware
	 */
	private $_subject;

	/**
	 * @var Logger|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockLogger;

	public function setUp() {

		$this->_mockLogger = $this->getMock(Logger::class, [], [], '', false);
		$this->_subject = new GentimeMiddleware();
		$this->_subject->setLogger($this->_mockLogger);
	}

	public function testProcessRequest() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$request = new Request();
		$route = new Route();
		$route_name = null;
		$this->_subject->processRequest($request, $route, $route_name);
	}

	public function testProcessResponse() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$request = new Request();
		$response = new Response();
		$this->_subject->processResponse($request, $response);
	}

}
