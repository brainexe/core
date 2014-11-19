<?php

namespace Tests\BrainExe\Core\Authentication\Controller\LogoutController;

use BrainExe\Core\Authentication\Controller\LogoutController;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Covers BrainExe\Core\Authentication\Controller\LogoutController
 */
class LogoutControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var LogoutController
	 */
	private $_subject;


	public function setUp() {


		$this->_subject = new LogoutController();

	}

	public function testLogout() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$request = new Request();
		$actual_result = $this->_subject->logout($request);
	}

}
