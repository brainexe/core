<?php

namespace Tests\BrainExe\Core\Authentication\Controller\UserController;

use BrainExe\Core\Authentication\Controller\UserController;
use BrainExe\Core\Authentication\UserVO;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Covers BrainExe\Core\Authentication\Controller\UserController
 */
class UserControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var UserController
	 */
	private $_subject;

	public function setUp() {
		$this->_subject = new UserController();
	}

	public function testGetCurrentUser() {
		$user_vo = new UserVO();
		$request = new Request();

		$request->attributes->set('user', $user_vo);

		$actual_result = $this->_subject->getCurrentUser($request);

		$this->assertEquals($user_vo, $actual_result);
	}

}
