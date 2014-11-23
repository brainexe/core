<?php

namespace Tests\BrainExe\Core\Application\AppKernel;

use BrainExe\Core\Application\AppKernel;
use BrainExe\Core\Application\ControllerResolver;
use BrainExe\Core\Middleware\MiddlewareInterface;
use Exception;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouteCollection;

/**
 * @Covers BrainExe\Core\Application\AppKernel
 */
class AppKernelTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var AppKernel
	 */
	private $_subject;

	/**
	 * @var ControllerResolver|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockControllerResolver;

	/**
	 * @var RouteCollection|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockRouteCollection;

	/**
	 * @var MiddlewareInterface|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockMiddleWare;

	public function setUp() {
		$this->_mockControllerResolver = $this->getMock(ControllerResolver::class, [], [], '', false);
		$this->_mockRouteCollection = $this->getMock(RouteCollection::class, [], [], '', false);
		$this->_mockMiddleWare = $this->getMock(MiddlewareInterface::class, [], [], '', false);

		$this->_subject = new AppKernel($this->_mockControllerResolver, $this->_mockRouteCollection);

		$this->_subject->setMiddlewares([$this->_mockMiddleWare]);
	}

	public function testHandle() {
		$request = new Request();

		$exception_response = new Response();

		$this->_mockMiddleWare
			->expects($this->once())
			->method('processException')
			->with($request, $this->isInstanceOf(Exception::class))
			->will($this->returnValue($exception_response));

		$this->_mockMiddleWare
			->expects($this->once())
			->method('processResponse')
			->with($request, $exception_response)
			->will($this->returnValue($exception_response));

		$actual_result = $this->_subject->handle($request, $type = 1, $catch = 1);


		$this->assertEquals($exception_response, $actual_result);
//		print_r($result);
	}

}
