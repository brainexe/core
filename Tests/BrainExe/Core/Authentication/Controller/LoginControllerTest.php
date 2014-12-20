<?php

namespace Tests\BrainExe\Core\Authentication\Controller\LoginController;

use BrainExe\Core\Authentication\Controller\LoginController;
use BrainExe\Core\Authentication\Login;
use BrainExe\Core\Authentication\UserVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

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
		$username       = 'username';
		$plain_password = 'plain password';
		$one_time_token = 'one time token';

		$session = new Session(new MockArraySessionStorage());

		$request = new Request();
		$request->request->set('username', $username);
		$request->request->set('password', $plain_password);
		$request->request->set('one_time_token', $one_time_token);
		$request->setSession($session);

		$user_vo = new UserVO();

		$this->_mockLogin
			->expects($this->once())
			->method('tryLogin')
			->with($username, $plain_password, $one_time_token, $session)
			->will($this->returnValue($user_vo));

		$actual_result = $this->_subject->doLogin($request);

		$this->assertInstanceOf(JsonResponse::class, $actual_result);
	}

}
