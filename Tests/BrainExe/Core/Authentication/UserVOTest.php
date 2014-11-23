<?php

namespace Tests\BrainExe\Core\Authentication\RegisterTokens;

use BrainExe\Core\Authentication\UserVO;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Security\Core\Role\Role;

/**
 * @Covers BrainExe\Core\Authentication\UserVO
 */
class UserVOTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var UserVO
	 */
	private $_subject;

	public function setUp() {
		$this->_subject = new UserVO();
	}

	public function testRoles() {
		$this->_subject->roles = [
			'role_1',
			'role_2'
		];

		$this->assertTrue($this->_subject->hasRole('role_1'));
		$this->assertTrue($this->_subject->hasRole('role_2'));
		$this->assertFalse($this->_subject->hasRole('role_444'));

		$actual_roles   = $this->_subject->getRoles();
		$expected_roles = [
			new Role('role_1'),
			new Role('role_2'),
		];

		$this->assertEquals($expected_roles, $actual_roles);
	}

	public function testToJson() {
		$this->_subject->username      = $username = 'username';
		$this->_subject->id            = $id = 42;
		$this->_subject->password      = 'password';
		$this->_subject->password_hash = 'password_hash';

		$actual_result = $this->_subject->jsonSerialize();

		$expected_result = [
			'username' => $username,
			'id' => $id,
		];
		$this->assertEquals($expected_result, $actual_result);
	}

	public function testPassword() {
		$this->_subject->password = 'password';
		$this->_subject->password_hash = 'password_hash';

		$this->_subject->eraseCredentials();

		$this->assertNull($this->_subject->password);
		$this->assertNull($this->_subject->password_hash);
	}

	public function testGetSalt() {
		$this->_subject->username = $username = 'username';

		$this->assertEquals($username, $this->_subject->getSalt());
	}

}
