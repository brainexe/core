<?php

namespace Tests\BrainExe\Core\Middleware\CsrfMiddleware;

use BrainExe\Core\Middleware\CsrfMiddleware;
use BrainExe\Core\Util\IdGenerator;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

/**
 * @Covers BrainExe\Core\Middleware\CsrfMiddleware
 */
class CsrfMiddlewareTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var CsrfMiddleware
	 */
	private $_subject;

	/**
	 * @var IdGenerator|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockIdGenerator;

	public function setUp() {

		$this->_mockIdGenerator = $this->getMock(IdGenerator::class, [], [], '', false);
		$this->_subject = new CsrfMiddleware();
		$this->_subject->setIdGenerator($this->_mockIdGenerator);
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
