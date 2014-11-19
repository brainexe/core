<?php

namespace Tests\BrainExe\Core\Authentication\Controller\LoginController;

use BrainExe\Core\Authentication\Controller\LoginController;
use BrainExe\Core\Authentication\Login;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Covers BrainExe\Core\Authentication\Controller\LoginController
 */
class LoginControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var LoginController
	 */
	private $_subject;

	/**
	 * @var Login|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockLogin;

	public function setUp() {

		$this->_mockLogin = $this->getMock(Login::class, [], [], '', false);
		$this->_subject = new LoginController($this->_mockLogin);

	}

	public function testDoLogin() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$request = new Request();
		$actual_result = $this->_subject->doLogin($request);
	}

}
