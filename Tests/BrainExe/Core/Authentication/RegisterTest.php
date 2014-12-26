<?php

namespace Tests\BrainExe\Core\Authentication\Register;

use BrainExe\Core\Authentication\DatabaseUserProvider;
use BrainExe\Core\Authentication\Register;
use BrainExe\Core\Authentication\RegisterTokens;
use BrainExe\Core\Authentication\UserVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * @Covers BrainExe\Core\Authentication\Register
 */
class RegisterTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Register
     */
    private $subject;

    /**
     * @var DatabaseUserProvider|MockObject
     */
    private $mockDatabaseUserProvider;

    /**
     * @var RegisterTokens|MockObject
     */
    private $mockRegisterTokens;

    public function setUp()
    {
        $this->mockDatabaseUserProvider = $this->getMock(DatabaseUserProvider::class, [], [], '', false);
        $this->mockRegisterTokens = $this->getMock(RegisterTokens::class, [], [], '', false);

        $this->subject = new Register($this->mockDatabaseUserProvider, $this->mockRegisterTokens, false);
    }

    /**
     * @expectedException \BrainExe\Core\Application\UserException
     * @expectedExceptionMessage User user name already exists
     */
    public function testRegisterWithUsernameAlreadyExists()
    {
        $user = new UserVO();
        $user->username = $username = 'user name';

        $session = new Session();
        $token = 100;

        $this->mockDatabaseUserProvider
        ->expects($this->once())
        ->method('loadUserByUsername')
        ->with($username)
        ->willReturn($user);

        $this->subject->registerUser($user, $session, $token);
    }

    /**
     * @expectedException \BrainExe\Core\Application\UserException
     * @expectedExceptionMessage You have to provide a valid register token!
     */
    public function testRegisterWithInvalidToken()
    {
        $user = new UserVO();
        $user->username = $username = 'user name';

        $session = new Session(new MockArraySessionStorage());
        $token = 100;

        $this->mockDatabaseUserProvider
        ->expects($this->once())
        ->method('loadUserByUsername')
        ->with($username)
        ->will($this->throwException(new UsernameNotFoundException()));

        $this->mockRegisterTokens
        ->expects($this->once())
        ->method('fetchToken')
        ->with($token)
        ->willReturn(false);

        $this->subject->registerUser($user, $session, $token);
    }

    public function testRegisterWithValidToken()
    {
        $user = new UserVO();
        $user->username = $username = 'user name';

        $userId = 42;
        $session = new Session(new MockArraySessionStorage());
        $token   = 100;

        $this->mockDatabaseUserProvider
        ->expects($this->once())
        ->method('loadUserByUsername')
        ->with($username)
        ->will($this->throwException(new UsernameNotFoundException()));

        $this->mockRegisterTokens
        ->expects($this->once())
        ->method('fetchToken')
        ->with($token)
        ->willReturn(true);

        $this->mockDatabaseUserProvider
        ->expects($this->once())
        ->method('register')
        ->with($user)
        ->willReturn($userId);

        $actualResult = $this->subject->registerUser($user, $session, $token);

        $this->assertEquals($userId, $actualResult);
        $this->assertEquals($user, $session->get('user'));
    }
}
