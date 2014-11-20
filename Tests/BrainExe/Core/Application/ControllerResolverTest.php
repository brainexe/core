<?php

namespace Tests\BrainExe\Core\Application\ControllerResolver;

use BrainExe\Core\Application\ControllerResolver;
use BrainExe\Core\DependencyInjection\ObjectFinder;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Covers BrainExe\Core\Application\ControllerResolver
 */
class ControllerResolverTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var ControllerResolver
	 */
	private $_subject;

	/**
	 * @var ObjectFinder|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockObjectFinder;

	public function setUp() {
		$this->_mockObjectFinder = $this->getMock(ObjectFinder::class, [], [], '', false);

		$this->_subject = new ControllerResolver();
		$this->_subject->setObjectFinder($this->_mockObjectFinder);
	}

	public function testGetController() {
		$request = new Request();
		$this->_subject->getController($request);
	}

	public function testGetArguments() {
		$request = new Request();
		$controller = null;

		$request->attributes->set('key1', 'value1');
		$request->attributes->set('key2', 'value2');

		$actual_result = $this->_subject->getArguments($request, $controller);

		$expected_result = [
			$request, 'value1', 'value2'
		];

		$this->assertEquals($expected_result, $actual_result);
	}

}
