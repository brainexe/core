<?php

namespace Tests\BrainExe\Core\Application\AppKernel;

use BrainExe\Core\Application\AppKernel;
use BrainExe\Core\Application\ControllerResolver;
use Monolog\Logger;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
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
	 * @var Logger|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockLogger;

	public function setUp() {

		$this->_mockControllerResolver = $this->getMock(ControllerResolver::class, [], [], '', false);
		$this->_mockRouteCollection = $this->getMock(RouteCollection::class, [], [], '', false);
		$this->_mockLogger = $this->getMock(Logger::class, [], [], '', false);
		$this->_subject = new AppKernel($this->_mockControllerResolver, $this->_mockRouteCollection);
		$this->_subject->setLogger($this->_mockLogger);
	}

	public function testSetMiddlewares() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$middlewares = null;
		$this->_subject->setMiddlewares($middlewares);
	}

	public function testHandle() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$request = new Request();
		$type = 1;
		$catch = 1;
		$this->_subject->handle($request, $type, $catch);
	}

}
