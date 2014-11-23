<?php

namespace Tests\BrainExe\Core\Authentication\Controller\LogoutController;

use BrainExe\Core\Authentication\AnonymusUserVO;
use BrainExe\Core\Authentication\Controller\LogoutController;
use BrainExe\Core\Authentication\UserVO;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

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
		$user = new UserVO();

		$session = new Session(new MockArraySessionStorage());
		$session->set('user', $user);

		$request = new Request();
		$request->setSession($session);

		$this->assertEquals($user, $session->get('user'));

		$actual_result = $this->_subject->logout($request);

		$this->assertNull($session->get('user'));
		$this->assertInstanceOf(AnonymusUserVO::class, $actual_result);
	}

}
