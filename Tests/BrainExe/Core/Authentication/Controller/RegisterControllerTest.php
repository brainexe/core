<?php

namespace Tests\BrainExe\Core\Authentication\Controller\RegisterController;

use BrainExe\Core\Authentication\Controller\RegisterController;
use BrainExe\Core\Authentication\Register;
use BrainExe\Core\Authentication\UserVO;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * @Covers BrainExe\Core\Authentication\Controller\RegisterController
 */
class RegisterControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var RegisterController
	 */
	private $_subject;

	/**
	 * @var Register|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockRegister;

	public function setUp() {
		$this->_mockRegister = $this->getMock(Register::class, [], [], '', false);

		$this->_subject = new RegisterController($this->_mockRegister);
	}

	public function testDoRegister() {
		$username       = 'username';
		$plain_password = 'plain password';
		$token          = 'token';

		$session = new Session(new MockArraySessionStorage());

		$request = new Request();
		$request->request->set('username', $username);
		$request->request->set('password', $plain_password);
		$request->cookies->set('token', $token);
		$request->setSession($session);

		$user_vo           = new UserVO();
		$user_vo->username = $username;
		$user_vo->password = $plain_password;

		$this->_mockRegister
			->expects($this->once())
			->method('register')
			->with($user_vo, $session, $token);

		$actual_result = $this->_subject->doRegister($request);

		$this->assertInstanceOf(JsonResponse::class, $actual_result);
	}

}
