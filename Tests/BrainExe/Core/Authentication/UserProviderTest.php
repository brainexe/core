<?php

namespace Tests\BrainExe\Core\Authentication;

use BrainExe\Core\Authentication\Exception\UserNotFoundException;
use BrainExe\Core\Authentication\UserProvider;
use BrainExe\Core\Authentication\LoadUser;
use BrainExe\Core\Authentication\PasswordHasher;
use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Redis\Predis;
use BrainExe\Core\Util\IdGenerator;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers BrainExe\Core\Authentication\UserProvider
 */
class UserProviderTest extends TestCase
{

    use RedisMockTrait;

    /**
     * @var UserProvider
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

    /**
     * @var LoadUser|MockObject
     */
    private $loadUser;

    public function setUp()
    {
        $this->redis          = $this->getRedisMock();
        $this->idGenerator    = $this->createMock(IdGenerator::class);
        $this->loadUser       = $this->createMock(LoadUser::class);
        $this->hasher         = $this->createMock(PasswordHasher::class);
        $this->dispatcher     = $this->createMock(EventDispatcher::class);

        $this->subject = new UserProvider($this->hasher, $this->loadUser);
        $this->subject->setRedis($this->redis);
        $this->subject->setIdGenerator($this->idGenerator);
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testLoadUserByUsername()
    {
        $username = 'foobar';
        $user     = new UserVO();

        $this->loadUser
            ->expects($this->once())
            ->method('loadUserByUsername')
            ->with($username)
            ->willReturn($user);

        $actual = $this->subject->loadUserByUsername($username);

        $this->assertEquals($user, $actual);
    }

    public function testLoadUserById()
    {
        $userId = 42;
        $user   = new UserVO();

        $this->loadUser
            ->expects($this->once())
            ->method('loadUserById')
            ->with($userId)
            ->willReturn($user);

        $actual = $this->subject->loadUserById($userId);

        $this->assertEquals($user, $actual);
    }

    public function testGetAllUserNames()
    {
        $userNames = [1 => 'john', 12 => 'jane'];

        $this->redis
            ->expects($this->once())
            ->method('hgetall')
            ->with(UserProvider::REDIS_USER_NAMES)
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
            ->method('hset')
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
            ->method('hset')
            ->with("user:$userId", 'username', $username);

        $this->subject->setUserProperty($user, 'username');
    }

    public function testSetUserPropertyArray()
    {
        $user        = new UserVO();
        $user->id    = $userId = 42;
        $user->roles = ['foo', 'bar'];
        $this->redis
            ->expects($this->once())
            ->method('hset')
            ->with("user:$userId", 'roles', 'foo,bar');

        $this->subject->setUserProperty($user, 'roles');
    }

    public function testDelete()
    {
        $userId = 42;

        $user           = new UserVO();
        $user->id       = $userId;
        $user->username = 'UsErNaMe';

        $this->loadUser
            ->expects($this->once())
            ->method('loadUserById')
            ->with($userId)
            ->willReturn($user);

        $this->redis
            ->expects($this->once())
            ->method('hdel')
            ->with(UserProvider::REDIS_USER_NAMES, 'username');

        $this->redis
            ->expects($this->once())
            ->method('del')
            ->with('user:42');

        $actual = $this->subject->deleteUser($userId);

        $this->assertTrue($actual);
    }

    public function testDeleteNotExisting()
    {
        $userId = 42;

        $this->loadUser
            ->expects($this->once())
            ->method('loadUserById')
            ->with($userId)
            ->willThrowException(new UserNotFoundException());

        $actual = $this->subject->deleteUser($userId);

        $this->assertFalse($actual);
    }

    public function testRegister()
    {
        $userId = 42;

        $user = new UserVO();
        $user->username = $username = 'username';
        $user->password = 'password';

        $hash = 'mySecretHash';

        $this->idGenerator
            ->expects($this->once())
            ->method('generateUniqueId')
            ->willReturn($userId);

        $this->redis
            ->expects($this->once())
            ->method('pipeline')
            ->willReturn($this->redis);

        $this->hasher
            ->expects($this->once())
            ->method('generateHash')
            ->with($user->password)
            ->willReturn($hash);

        $this->redis
            ->expects($this->once())
            ->method('hset')
            ->with(UserProvider::REDIS_USER_NAMES, $username, $userId)
            ->willReturn($this->redis);

        $this->redis
            ->expects($this->once())
            ->method('hmset')
            ->with("user:$userId", $this->isType('array'))
            ->willReturn($this->redis);

        $this->redis
            ->expects($this->once())
            ->method('execute');

        $actualResult = $this->subject->register($user);

        $this->assertEquals($userId, $actualResult);
    }
}
