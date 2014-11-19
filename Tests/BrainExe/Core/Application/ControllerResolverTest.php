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
		$this->markTestIncomplete('This is only a dummy implementation');

		$request = new Request();
		$this->_subject->getController($request);
	}

	public function testGetArguments() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$request = new Request();
		$controller = null;
		$this->_subject->getArguments($request, $controller);
	}

}
