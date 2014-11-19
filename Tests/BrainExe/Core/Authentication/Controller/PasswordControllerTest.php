<?php

namespace Tests\BrainExe\Core\Authentication\Controller\PasswordController;

use BrainExe\Core\Authentication\Controller\PasswordController;
use BrainExe\Core\Authentication\DatabaseUserProvider;
use BrainExe\Core\Authentication\UserVO;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Covers BrainExe\Core\Authentication\Controller\PasswordController
 */
class PasswordControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var PasswordController
	 */
	private $_subject;

	/**
	 * @var DatabaseUserProvider|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockDatabaseUserProvider;

	public function setUp() {
		$this->_mockDatabaseUserProvider = $this->getMock(DatabaseUserProvider::class, [], [], '', false);

		$this->_subject = new PasswordController($this->_mockDatabaseUserProvider);
	}

	public function testChangePassword() {
		$password = 'password';
		$user     = new UserVO();

		$request = new Request();
		$request->request->set('password', $password);
		$request->attributes->set('user', $user);

		$this->_mockDatabaseUserProvider
			->expects($this->once())
			->method('changePassword')
			->with($user, $password);

		$actual_result = $this->_subject->changePassword($request);
		$this->assertTrue($actual_result);
	}

}
