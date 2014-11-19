<?php

namespace BrainExe\Tests\Core\Application;

use BrainExe\Core\Application\AppKernel;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;

class AppKernelTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var AppKernel
	 */
	private $_subject;

	public function setUp() {
		/** @var $dic ContainerBuilder */
		global $dic;

		$this->_subject = $dic->get('AppKernel');

//		$error_view = $this->getMock(ErrorView::class, [], [], '', false);
//		$dic->set('ErrorView', $error_view);
	}


	public function testHandleInvalidRoute() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$request = new Request();

		$response = $this->_subject->handle($request);

		$this->assertEquals(404, $response->getStatusCode());
	}
}
