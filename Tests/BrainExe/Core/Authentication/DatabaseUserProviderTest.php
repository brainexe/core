<?php

namespace Tests\BrainExe\Core\Authentication\DatabaseUserProvider;

use BrainExe\Core\Authentication\DatabaseUserProvider;
use BrainExe\Core\Authentication\PasswordHasher;
use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Util\IdGenerator;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Redis;

/**
 * @Covers BrainExe\Core\Authentication\DatabaseUserProvider
 */
class DatabaseUserProviderTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var DatabaseUserProvider
	 */
	private $_subject;

	/**
	 * @var Redis|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockRedis;

	/**
	 * @var IdGenerator|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockIdGenerator;

	/**
	 * @var PasswordHasher|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockPasswordHasher;

	public function setUp() {
		$this->_mockRedis = $this->getMock(Redis::class, [], [], '', false);
		$this->_mockIdGenerator = $this->getMock(IdGenerator::class, [], [], '', false);
		$this->_mockPasswordHasher = $this->getMock(PasswordHasher::class, [], [], '', false);

		$this->_subject = new DatabaseUserProvider($this->_mockPasswordHasher);
		$this->_subject->setRedis($this->_mockRedis);
		$this->_subject->setIdGenerator($this->_mockIdGenerator);
	}

	/**
	 * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
	 * @expectedExceptionMessage Username "UserName" does not exist.
	 */
	public function testLoadUserByUsernameWithInvalidUser() {
		$username = 'UserName';

		$this->_mockRedis
			->expects($this->once())
			->method('HGET')
			->with(DatabaseUserProvider::REDIS_USER_NAMES, 'username')
			->will($this->returnValue(null));

		$this->_subject->loadUserByUsername($username);
	}

	public function testLoadUserByUsername() {
		$username = 'UserName';
		$user_id  = 41;

		$user_raw = [
			'username' => $username,
			'email' => $email = 'email@example.com',
			'password' => $password = 'password',
			'one_time_secret' => $one_time_secret = 'one_time_secret',
			'roles' => 'role_1,role_2'
		];

		$this->_mockRedis
			->expects($this->once())
			->method('HGET')
			->with(DatabaseUserProvider::REDIS_USER_NAMES, 'username')
			->will($this->returnValue($user_id));

		$this->_mockRedis
			->expects($this->once())
			->method('HGETALL')
			->with("user:$user_id")
			->will($this->returnValue($user_raw));

		$actual_result = $this->_subject->loadUserByUsername($username);

		$expected_user = new UserVO();
		$expected_user->id = $user_id;
		$expected_user->username = $username;
		$expected_user->email = $email;
		$expected_user->password_hash = $password;
		$expected_user->one_time_secret = $one_time_secret;
		$expected_user->roles = ['role_1', 'role_2'];

		$this->assertEquals($expected_user, $actual_result);
	}

	public function testGetAllUserNames() {
		$user_names = [1 => 'john', 12 => 'jane'];

		$this->_mockRedis
			->expects($this->once())
			->method('hGetAll')
			->with(DatabaseUserProvider::REDIS_USER_NAMES)
			->will($this->returnValue($user_names));

		$actual_result = $this->_subject->getAllUserNames();

		$this->assertEquals($user_names, $actual_result);
	}

	/**
	 * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
	 */
	public function testRefreshUser() {
		$user = new UserVO();
		$user->username = $username = 'username';

		$this->_mockRedis
			->expects($this->once())
			->method('HGET')
			->with(DatabaseUserProvider::REDIS_USER_NAMES, $username)
			->will($this->returnValue(false));


		$this->_subject->refreshUser($user);
	}

	public function testSupportsClassOfUserVO() {
		$actual_result = $this->_subject->supportsClass(UserVO::class);
		$this->assertTrue($actual_result);
	}

	public function testGenerateHash() {
		$password = 'password';
		$hash     = 'hash';

		$this->_mockPasswordHasher
			->expects($this->once())
			->method('generateHash')
			->with($password)
			->will($this->returnValue($hash));

		$actual_result = $this->_subject->generateHash($password);

		$this->assertEquals($hash, $actual_result);
	}

	public function testVerifyHash() {
		$password = 'password';
		$hash     = 'hash';

		$this->_mockPasswordHasher
			->expects($this->once())
			->method('verifyHash')
			->with($password, $hash)
			->will($this->returnValue(true));

		$actual_result = $this->_subject->verifyHash($password, $hash);

		$this->assertTrue($actual_result);
	}

	public function testChangePassword() {
		$user     = new UserVO();
		$user->id = $user_id = 42;

		$new_password = 'new_password';
		$hash         = 'hash';

		$this->_mockPasswordHasher
			->expects($this->once())
			->method('generateHash')
			->with($new_password)
			->will($this->returnValue($hash));

		$this->_mockRedis
			->expects($this->once())
			->method('hSet')
			->with("user:$user_id", 'password', $hash);

		$this->_subject->changePassword($user, $new_password);
	}

	public function testSetUserProperty() {
		$user           = new UserVO();
		$user->id       = $user_id = 42;
		$user->username = $username = 'username';

		$this->_mockRedis
			->expects($this->once())
			->method('hSet')
			->with("user:$user_id", 'username', $username);

		$this->_subject->setUserProperty($user, 'username');
	}

	public function testRegister() {
		$user_id = 42;

		$user = new UserVO();
		$user->username = $username = 'username';

		$this->_mockIdGenerator
			->expects($this->once())
			->method('generateRandomNumericId')
			->will($this->returnValue($user_id));

		$this->_mockRedis
			->expects($this->once())
			->method('multi')
			->will($this->returnValue($this->_mockRedis));

		$this->_mockRedis
			->expects($this->once())
			->method('HSET')
			->with(DatabaseUserProvider::REDIS_USER_NAMES, $username, $user_id)
			->will($this->returnValue($this->_mockRedis));

		$this->_mockRedis
			->expects($this->once())
			->method('HMSET')
			->with("user:$user_id", $this->isType('array')) // todo fuzzy
			->will($this->returnValue($this->_mockRedis));

		$this->_mockRedis
			->expects($this->once())
			->method('exec');

		$actual_result = $this->_subject->register($user);

		$this->assertEquals($user_id, $actual_result);
	}

}
