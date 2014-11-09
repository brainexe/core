<?php

namespace BrainExe\Tests\Core\Application;

use BrainExe\Core\Controller\ControllerInterface;
use BrainExe\Core\DependencyInjection\ObjectFinder;
use PHPUnit_Framework_MockObject_MockObject;
use BrainExe\Core\Application\ControllerResolver;
use Symfony\Component\HttpFoundation\Request;

class TestController implements ControllerInterface {

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
		$this->_mock_object_finder = $this->getMock('BrainExe\Core\DependencyInjection\ObjectFinder', [], [], '', false);
		$this->_subject = new ControllerResolver($this->_mock_object_finder);
		$this->_subject->setObjectFinder($this->_mock_object_finder);
	}

	public function testGetControllerWithInvalidControllerShouldReturnFalse() {
		$request = new Request();

		$result = $this->_subject->getController($request);

		$this->assertFalse($result);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage The controller for URI "/" is not callable
	 */
	public function testGetControllerWithInvalidAction() {
		$controller_name = 'test::invalidAction';

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

	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage Unable to find controller "invalid"
	 */
	public function testGetControllerWithInvalidActionString() {
		$controller_name = 'invalid';

		$request = new Request();
		$request->attributes->set('_controller', $controller_name);

		$this->_subject->getController($request);
	}

	public function testGetController() {
		$controller_name = 'test::validAction';

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

		$this->assertEquals([$request, null], $arguments);
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
