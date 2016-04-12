<?php

namespace Tests\BrainExe\Core\Authentication;

use BrainExe\Core\Authentication\UserProvider;
use BrainExe\Core\Authentication\LoadUser;
use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Redis\Predis;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers BrainExe\Core\Authentication\LoadUser
 */
class LoadUserTest extends TestCase
{

    use RedisMockTrait;

    /**
     * @var LoadUser
     */
    private $subject;

    /**
     * @var Predis|MockObject
     */
    private $redis;

    public function setUp()
    {
        $this->redis = $this->getRedisMock();

        $this->subject = new LoadUser($this->redis);
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
            ->method('hget')
            ->with(UserProvider::REDIS_USER_NAMES, 'username')
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
            ->method('hget')
            ->with(UserProvider::REDIS_USER_NAMES, 'username')
            ->willReturn($userId);

        $this->redis
            ->expects($this->once())
            ->method('hgetall')
            ->with("user:$userId")
            ->willReturn($userRaw);

        $actualResult = $this->subject->loadUserByUsername($username);

        $expectedUser                  = new UserVO();
        $expectedUser->id              = $userId;
        $expectedUser->username        = $username;
        $expectedUser->email           = $email;
        $expectedUser->password_hash   = $password;
        $expectedUser->avatar          = UserVO::AVATAR_5;
        $expectedUser->one_time_secret = $oneTimeSecret;
        $expectedUser->roles           = ['role_1', 'role_2'];

        $this->assertEquals($expectedUser, $actualResult);
    }
}
