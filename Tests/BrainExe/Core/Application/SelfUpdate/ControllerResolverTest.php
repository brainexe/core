<?php

namespace BrainExe\Tests\Core\Application;

use BrainExe\Core\Application\ControllerResolver;
use BrainExe\Core\Controller\ControllerInterface;
use BrainExe\Core\DependencyInjection\ObjectFinder;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Component\HttpFoundation\Request;

class TestController implements ControllerInterface {

	/**
	 * @param Request $request
	 * @param null $test
	 */
	public function validAction(Request $request, $test = null) {}
}

class ControllerResolverTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var ControllerResolver
	 */
	private $_subject;

	/**
	 * @var ObjectFinder|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mock_object_finder;

	protected function setUp() {
		$this->_mock_object_finder = $this->getMock(ObjectFinder::class, [], [], '', false);

		$this->_subject = new ControllerResolver($this->_mock_object_finder);
		$this->_subject->setObjectFinder($this->_mock_object_finder);
	}

	public function testGetController() {
		$controller_name = ['Controller.test', 'validAction'];

		$request = new Request();
		$request->attributes->set('_controller', $controller_name);

		$controller = new TestController();

		$this->_mock_object_finder
			->expects($this->once())
			->method('getService')
			->with('Controller.test')
			->will($this->returnValue($controller));

		$this->_subject->getController($request);
	}

	public function testGetArgumentsWithoutArguments() {
		$controller = new TestController();
		$request = new Request();

		$callable = [$controller, 'validAction'];

		$arguments = $this->_subject->getArguments($request, $callable);

		$this->assertEquals([$request], $arguments);
	}

	public function testGetArguments() {
		$controller = new TestController();
		$request = new Request();
		$request->attributes->set('test', 'foo');

		$callable = [$controller, 'validAction'];

		$arguments = $this->_subject->getArguments($request, $callable);

		$this->assertEquals([$request, 'foo'], $arguments);
	}
}
