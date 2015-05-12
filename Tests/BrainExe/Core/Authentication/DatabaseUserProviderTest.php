<?php

namespace Tests\BrainExe\Core\Authentication\DatabaseUserProvider;

use BrainExe\Core\Authentication\DatabaseUserProvider;
use BrainExe\Core\Authentication\PasswordHasher;
use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Redis\Predis;
use BrainExe\Core\Util\IdGenerator;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers BrainExe\Core\Authentication\DatabaseUserProvider
 */
class DatabaseUserProviderTest extends TestCase
{

    use RedisMockTrait;

    /**
     * @var DatabaseUserProvider
     */
    private $subject;

    /**
     * @var Predis|MockObject
     */
    private $redis;

    /**
     * @var IdGenerator|MockObject
     */
    private $idGenerator;

    /**
     * @var PasswordHasher|MockObject
     */
    private $hasher;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->redis          = $this->getRedisMock();
        $this->idGenerator    = $this->getMock(IdGenerator::class, [], [], '', false);
        $this->hasher         = $this->getMock(PasswordHasher::class, [], [], '', false);
        $this->dispatcher     = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new DatabaseUserProvider($this->hasher);
        $this->subject->setRedis($this->redis);
        $this->subject->setIdGenerator($this->idGenerator);
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    /**
     * @expectedException \BrainExe\Core\Authentication\Exception\UsernameNotFoundException
     * @expectedExceptionMessage Username "UserName" does not exist.
     */
    public function testLoadUserByUsernameWithInvalidUser()
    {
        $username = 'UserName';

        $this->redis
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

        $this->redis
            ->expects($this->once())
            ->method('HGET')
            ->with(DatabaseUserProvider::REDIS_USER_NAMES, 'username')
            ->willReturn($userId);

        $this->redis
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

        $this->redis
            ->expects($this->once())
            ->method('hGetAll')
            ->with(DatabaseUserProvider::REDIS_USER_NAMES)
            ->willReturn($userNames);

        $actualResult = $this->subject->getAllUserNames();

        $this->assertEquals($userNames, $actualResult);
    }

    public function testGenerateHash()
    {
        $password = 'password';
        $hash     = 'hash';

        $this->hasher
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

        $this->hasher
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
        $hash = 'hash';

        $this->hasher
            ->expects($this->once())
            ->method('generateHash')
            ->with($newPassword)
            ->willReturn($hash);

        $this->redis
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

        $this->redis
            ->expects($this->once())
            ->method('hSet')
            ->with("user:$userId", 'username', $username);

        $this->subject->setUserProperty($user, 'username');
    }

    public function testDelete()
    {
        $userId = 42;

        $user           = new UserVO();
        $user->id       = $userId;
        $user->username = 'UsErNaMe';

        $this->redis
            ->expects($this->once())
            ->method('hgetall')
            ->with('user:42')
            ->willReturn([
                'username' => 'UsErNaMe',
                'roles' => '',
                'one_time_secret' => '',
                'password' => ''
            ]);

        $this->redis
            ->expects($this->once())
            ->method('hdel')
            ->with(DatabaseUserProvider::REDIS_USER_NAMES, 'username');

        $this->redis
            ->expects($this->once())
            ->method('del')
            ->with('user:42');

        $this->subject->deleteUser($userId);
    }

    public function testDeleteNotExisting()
    {
        $userId = 42;

        $this->redis
            ->expects($this->once())
            ->method('hgetall')
            ->with('user:42')
            ->willReturn([]);

        $this->subject->deleteUser($userId);
    }

    public function testRegister()
    {
        $userId = 42;

        $user = new UserVO();
        $user->username = $username = 'username';

        $this->idGenerator
            ->expects($this->once())
            ->method('generateRandomNumericId')
            ->willReturn($userId);

        $this->redis
            ->expects($this->once())
            ->method('pipeline')
            ->willReturn($this->redis);

        $this->redis
            ->expects($this->once())
            ->method('HSET')
            ->with(DatabaseUserProvider::REDIS_USER_NAMES, $username, $userId)
            ->willReturn($this->redis);

        $this->redis
            ->expects($this->once())
            ->method('HMSET')
            ->with("user:$userId", $this->isType('array')) // todo fuzzy
            ->willReturn($this->redis);

        $this->redis
            ->expects($this->once())
            ->method('execute');

        $actualResult = $this->subject->register($user);

        $this->assertEquals($userId, $actualResult);
    }
}
