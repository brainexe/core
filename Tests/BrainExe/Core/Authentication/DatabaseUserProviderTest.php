<?php

namespace Tests\BrainExe\Core\Authentication;

use BrainExe\Core\Authentication\AnonymusUserVO;
use BrainExe\Core\Authentication\DatabaseUserProvider;
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

    /**
     * @var LoadUser|MockObject
     */
    private $loadUser;

    public function setUp()
    {
        $this->redis          = $this->getRedisMock();
        $this->idGenerator    = $this->getMock(IdGenerator::class, [], [], '', false);
        $this->loadUser       = $this->getMock(LoadUser::class, [], [], '', false);
        $this->hasher         = $this->getMock(PasswordHasher::class, [], [], '', false);
        $this->dispatcher     = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new DatabaseUserProvider($this->hasher, $this->loadUser);
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

        $user = new AnonymusUserVO();

        $this->loadUser
            ->expects($this->once())
            ->method('loadUserById')
            ->with($userId)
            ->willReturn($user);

        $this->subject->deleteUser($userId);
    }

    public function testRegister()
    {
        $userId = 42;

        $user = new UserVO();
        $user->username = $username = 'username';

        $this->idGenerator
            ->expects($this->once())
            ->method('generateUniqueId')
            ->willReturn($userId);

        $this->redis
            ->expects($this->once())
            ->method('pipeline')
            ->willReturn($this->redis);

        $this->redis
            ->expects($this->once())
            ->method('hset')
            ->with(DatabaseUserProvider::REDIS_USER_NAMES, $username, $userId)
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
