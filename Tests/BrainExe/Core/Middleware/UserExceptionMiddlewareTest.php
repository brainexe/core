<?php

namespace Tests\BrainExe\Core\Middleware\UserExceptionMiddleware;

use BrainExe\Core\DependencyInjection\ObjectFinder;
use BrainExe\Core\Middleware\UserExceptionMiddleware;
use Exception;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * @Covers BrainExe\Core\Middleware\UserExceptionMiddleware
 */
class UserExceptionMiddlewareTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var UserExceptionMiddleware
	 */
	private $_subject;

	/**
	 * @var ObjectFinder|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockObjectFinder;

	public function setUp() {
		$this->_mockObjectFinder = $this->getMock(ObjectFinder::class, [], [], '', false);

		$this->_subject = new UserExceptionMiddleware();
		$this->_subject->setObjectFinder($this->_mockObjectFinder);
	}

	public function testProcessResourceNotFoundException() {
		$this->markTestIncomplete('This is only a dummy implementation');

		/** @var Request|PHPUnit_Framework_MockObject_MockObject $request */
		$request = $this->getMock(Request::class, ['isXmlHttpRequest']);

		$exception = new ResourceNotFoundException();

		$request
			->expects($this->once())
			->method('isXmlHttpRequest')
			->will($this->returnValue(true));

		$actual_result = $this->_subject->processException($request, $exception);

		$this->assertEquals(500, $actual_result->getStatusCode());
		$this->assertTrue($actual_result->headers->has('X-Flash'));
	}

}
