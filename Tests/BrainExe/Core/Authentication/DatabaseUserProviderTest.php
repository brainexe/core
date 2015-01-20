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
class DatabaseUserProviderTest extends PHPUnit_Framework_TestCase
{

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

    public function setUp()
    {
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
    public function testLoadUserByUsernameWithInvalidUser()
    {
        $username = 'UserName';

        $this->mockRedis
            ->expects($this->once())
            ->method('HGET')
            ->with(DatabaseUserProvider::REDIS_USER_NAMES, 'username')
            ->willReturn(null);

        $this->subject->loadUserByUsername($username);
    }

    public function testLoadUserByUsername()
    {
        $username = 'UserName';
        $userId  = 41;

        $userRaw = [
            'username' => $username,
            'email' => $email = 'email@example.com',
            'password' => $password = 'password',
            'one_time_secret' => $oneTimeSecret = 'one_time_secret',
        'roles' => 'role_1,role_2'
        ];

        $this->mockRedis
            ->expects($this->once())
            ->method('HGET')
            ->with(DatabaseUserProvider::REDIS_USER_NAMES, 'username')
            ->willReturn($userId);

        $this->mockRedis
            ->expects($this->once())
            ->method('HGETALL')
            ->with("user:$userId")
            ->willReturn($userRaw);

        $actualResult = $this->subject->loadUserByUsername($username);

        $expectedUser                  = new UserVO();
        $expectedUser->id              = $userId;
        $expectedUser->username        = $username;
        $expectedUser->email           = $email;
        $expectedUser->password_hash   = $password;
        $expectedUser->one_time_secret = $oneTimeSecret;
        $expectedUser->roles           = ['role_1', 'role_2'];

        $this->assertEquals($expectedUser, $actualResult);
    }

    public function testGetAllUserNames()
    {
        $userNames = [1 => 'john', 12 => 'jane'];

        $this->mockRedis
            ->expects($this->once())
            ->method('hGetAll')
            ->with(DatabaseUserProvider::REDIS_USER_NAMES)
            ->willReturn($userNames);

        $actualResult = $this->subject->getAllUserNames();

        $this->assertEquals($userNames, $actualResult);
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testRefreshUser()
    {
        $user = new UserVO();
        $user->username = $username = 'username';

        $this->mockRedis
            ->expects($this->once())
            ->method('HGET')
            ->with(DatabaseUserProvider::REDIS_USER_NAMES, $username)
            ->willReturn(false);


        $this->subject->refreshUser($user);
    }

    public function testSupportsClassOfUserVO()
    {
        $actualResult = $this->subject->supportsClass(UserVO::class);
        $this->assertTrue($actualResult);
    }

    public function testGenerateHash()
    {
        $password = 'password';
        $hash     = 'hash';

        $this->mockPasswordHasher
            ->expects($this->once())
            ->method('generateHash')
            ->with($password)
            ->willReturn($hash);

        $actualResult = $this->subject->generateHash($password);

        $this->assertEquals($hash, $actualResult);
    }

    public function testVerifyHash()
    {
        $password = 'password';
        $hash     = 'hash';

        $this->mockPasswordHasher
            ->expects($this->once())
            ->method('verifyHash')
            ->with($password, $hash)
            ->willReturn(true);

        $actualResult = $this->subject->verifyHash($password, $hash);

        $this->assertTrue($actualResult);
    }

    public function testChangePassword()
    {
        $user     = new UserVO();
        $user->id = $userId = 42;

        $newPassword = 'new_password';
        $hash         = 'hash';

        $this->mockPasswordHasher
            ->expects($this->once())
            ->method('generateHash')
            ->with($newPassword)
            ->willReturn($hash);

        $this->mockRedis
            ->expects($this->once())
            ->method('hSet')
            ->with("user:$userId", 'password', $hash);

        $this->subject->changePassword($user, $newPassword);
    }

    public function testSetUserProperty()
    {
        $user           = new UserVO();
        $user->id       = $userId = 42;
        $user->username = $username = 'username';

        $this->mockRedis
            ->expects($this->once())
            ->method('hSet')
            ->with("user:$userId", 'username', $username);

        $this->subject->setUserProperty($user, 'username');
    }

    public function testRegister()
    {
        $userId = 42;

        $user = new UserVO();
        $user->username = $username = 'username';

        $this->mockIdGenerator
            ->expects($this->once())
            ->method('generateRandomNumericId')
            ->willReturn($userId);

        $this->mockRedis
            ->expects($this->once())
            ->method('multi')
            ->willReturn($this->mockRedis);

        $this->mockRedis
            ->expects($this->once())
            ->method('HSET')
            ->with(DatabaseUserProvider::REDIS_USER_NAMES, $username, $userId)
            ->willReturn($this->mockRedis);

        $this->mockRedis
            ->expects($this->once())
            ->method('HMSET')
            ->with("user:$userId", $this->isType('array')) // todo fuzzy
            ->willReturn($this->mockRedis);

        $this->mockRedis
            ->expects($this->once())
            ->method('exec');

        $actualResult = $this->subject->register($user);

        $this->assertEquals($userId, $actualResult);
    }
}
