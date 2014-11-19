<?php

namespace Tests\BrainExe\Core\Application\ErrorView;

use BrainExe\Core\Application\ErrorView;
use BrainExe\Template\TwigEnvironment;
use Exception;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Covers BrainExe\Core\Application\ErrorView
 */
class ErrorViewTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var ErrorView
	 */
	private $_subject;

	/**
	 * @var TwigEnvironment|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockTwigEnvironment;

	public function setUp() {

		$this->_mockTwigEnvironment = $this->getMock(TwigEnvironment::class, [], [], '', false);
		$this->_subject = new ErrorView(false, 'error.html.twig');
		$this->_subject->setTwig($this->_mockTwigEnvironment);
	}

	public function testRenderException() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$request = new Request();
		$exception = new Exception();
		$actual_result = $this->_subject->renderException($request, $exception);
	}

}
