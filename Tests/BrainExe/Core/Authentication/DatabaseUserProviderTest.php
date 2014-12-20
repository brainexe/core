<?php

namespace Tests\BrainExe\Core\Authentication\DatabaseUserProvider;

use BrainExe\Core\Authentication\DatabaseUserProvider;
use BrainExe\Core\Authentication\PasswordHasher;
use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Util\IdGenerator;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use BrainExe\Core\Redis\Redis;

/**
 * @Covers BrainExe\Core\Authentication\DatabaseUserProvider
 */
class DatabaseUserProviderTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var DatabaseUserProvider
	 */
	private $subject;

	/**
	 * @var Redis|MockObject
	 */
	private $mockRedis;

	/**
	 * @var IdGenerator|MockObject
	 */
	private $mockIdGenerator;

	/**
	 * @var PasswordHasher|MockObject
	 */
	private $mockPasswordHasher;

	public function setUp() {
		$this->mockRedis = $this->getMock(Redis::class, [], [], '', false);
		$this->mockIdGenerator = $this->getMock(IdGenerator::class, [], [], '', false);
		$this->mockPasswordHasher = $this->getMock(PasswordHasher::class, [], [], '', false);

		$this->subject = new DatabaseUserProvider($this->mockPasswordHasher);
		$this->subject->setRedis($this->mockRedis);
		$this->subject->setIdGenerator($this->mockIdGenerator);
	}

	/**
	 * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
	 * @expectedExceptionMessage Username "UserName" does not exist.
	 */
	public function testLoadUserByUsernameWithInvalidUser() {
		$username = 'UserName';

		$this->mockRedis
			->expects($this->once())
			->method('HGET')
			->with(DatabaseUserProvider::REDIS_USER_NAMES, 'username')
			->will($this->returnValue(null));

		$this->subject->loadUserByUsername($username);
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

		$this->mockRedis
			->expects($this->once())
			->method('HGET')
			->with(DatabaseUserProvider::REDIS_USER_NAMES, 'username')
			->will($this->returnValue($user_id));

		$this->mockRedis
			->expects($this->once())
			->method('HGETALL')
			->with("user:$user_id")
			->will($this->returnValue($user_raw));

		$actual_result = $this->subject->loadUserByUsername($username);

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

		$this->mockRedis
			->expects($this->once())
			->method('hGetAll')
			->with(DatabaseUserProvider::REDIS_USER_NAMES)
			->will($this->returnValue($user_names));

		$actual_result = $this->subject->getAllUserNames();

		$this->assertEquals($user_names, $actual_result);
	}

	/**
	 * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
	 */
	public function testRefreshUser() {
		$user = new UserVO();
		$user->username = $username = 'username';

		$this->mockRedis
			->expects($this->once())
			->method('HGET')
			->with(DatabaseUserProvider::REDIS_USER_NAMES, $username)
			->will($this->returnValue(false));


		$this->subject->refreshUser($user);
	}

	public function testSupportsClassOfUserVO() {
		$actual_result = $this->subject->supportsClass(UserVO::class);
		$this->assertTrue($actual_result);
	}

	public function testGenerateHash() {
		$password = 'password';
		$hash     = 'hash';

		$this->mockPasswordHasher
			->expects($this->once())
			->method('generateHash')
			->with($password)
			->will($this->returnValue($hash));

		$actual_result = $this->subject->generateHash($password);

		$this->assertEquals($hash, $actual_result);
	}

	public function testVerifyHash() {
		$password = 'password';
		$hash     = 'hash';

		$this->mockPasswordHasher
			->expects($this->once())
			->method('verifyHash')
			->with($password, $hash)
			->will($this->returnValue(true));

		$actual_result = $this->subject->verifyHash($password, $hash);

		$this->assertTrue($actual_result);
	}

	public function testChangePassword() {
		$user     = new UserVO();
		$user->id = $user_id = 42;

		$new_password = 'new_password';
		$hash         = 'hash';

		$this->mockPasswordHasher
			->expects($this->once())
			->method('generateHash')
			->with($new_password)
			->will($this->returnValue($hash));

		$this->mockRedis
			->expects($this->once())
			->method('hSet')
			->with("user:$user_id", 'password', $hash);

		$this->subject->changePassword($user, $new_password);
	}

	public function testSetUserProperty() {
		$user           = new UserVO();
		$user->id       = $user_id = 42;
		$user->username = $username = 'username';

		$this->mockRedis
			->expects($this->once())
			->method('hSet')
			->with("user:$user_id", 'username', $username);

		$this->subject->setUserProperty($user, 'username');
	}

	public function testRegister() {
		$user_id = 42;

		$user = new UserVO();
		$user->username = $username = 'username';

		$this->mockIdGenerator
			->expects($this->once())
			->method('generateRandomNumericId')
			->will($this->returnValue($user_id));

		$this->mockRedis
			->expects($this->once())
			->method('multi')
			->will($this->returnValue($this->mockRedis));

		$this->mockRedis
			->expects($this->once())
			->method('HSET')
			->with(DatabaseUserProvider::REDIS_USER_NAMES, $username, $user_id)
			->will($this->returnValue($this->mockRedis));

		$this->mockRedis
			->expects($this->once())
			->method('HMSET')
			->with("user:$user_id", $this->isType('array')) // todo fuzzy
			->will($this->returnValue($this->mockRedis));

		$this->mockRedis
			->expects($this->once())
			->method('exec');

		$actual_result = $this->subject->register($user);

		$this->assertEquals($user_id, $actual_result);
	}

}
