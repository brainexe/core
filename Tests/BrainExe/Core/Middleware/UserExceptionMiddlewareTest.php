<?php

namespace Tests\BrainExe\Core\Middleware\UserExceptionMiddleware;

use BrainExe\Core\DependencyInjection\ObjectFinder;
use BrainExe\Core\Middleware\UserExceptionMiddleware;
use Exception;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;

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

	public function testProcessException() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$request = new Request();
		$exception = new Exception();
		$this->_subject->processException($request, $exception);
	}

}
