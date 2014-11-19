<?php

namespace Tests\BrainExe\Core\Authentication\Register;


use BrainExe\Core\Authentication\DatabaseUserProvider;
use BrainExe\Core\Authentication\Register;
use BrainExe\Core\Authentication\RegisterTokens;
use BrainExe\Core\Authentication\UserVO;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * @Covers BrainExe\Core\Authentication\Register
 */
class RegisterTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Register
	 */
	private $_subject;

	/**
	 * @var DatabaseUserProvider|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockDatabaseUserProvider;

	/**
	 * @var RegisterTokens|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockRegisterTokens;

	public function setUp() {
		$this->_mockDatabaseUserProvider = $this->getMock(DatabaseUserProvider::class, [], [], '', false);
		$this->_mockRegisterTokens = $this->getMock(RegisterTokens::class, [], [], '', false);

		$this->_subject = new Register($this->_mockDatabaseUserProvider, $this->_mockRegisterTokens, true);
	}

	/**
	 * @expectedException \BrainExe\Core\Application\UserException
	 * @expectedExceptionMessage User user name already exists
	 */
	public function testRegisterWithUsernameAlreadyExists() {
		$user = new UserVO();
		$user->username = $username = 'user name';

		$session = new Session();
		$token = 100;

		$this->_mockDatabaseUserProvider
			->expects($this->once())
			->method('loadUserByUsername')
			->with($username)
			->will($this->returnValue($user));

		$this->_subject->register($user, $session, $token);
	}

	public function testRegister() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$user = new UserVO();
		$user->username = $username = 'user name';

		$session = new Session(new MockArraySessionStorage());
		$token = 100;

		$this->_mockDatabaseUserProvider
			->expects($this->once())
			->method('loadUserByUsername')
			->with($username)
			->will($this->throwException(new UsernameNotFoundException()));

		$this->_mockRegisterTokens
			->expects($this->once())
			->method('fetchToken')
			->with($token)
			->will($this->returnValue(false));

		$actual_result = $this->_subject->register($user, $session, $token);
	}

}
