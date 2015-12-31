<?php

namespace Tests\BrainExe\Core\Authentication;

use BrainExe\Core\Authentication\AuthenticationDataVO;
use BrainExe\Core\Authentication\Event\AuthenticateUserEvent;
use BrainExe\Core\Authentication\Exception\UsernameNotFoundException;
use BrainExe\Core\Authentication\LoadUser;
use BrainExe\Core\Authentication\Login;
use BrainExe\Core\Authentication\PasswordHasher;
use BrainExe\Core\Authentication\Token;
use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * @covers BrainExe\Core\Authentication\Login
 */
class LoginTest extends TestCase
{

    /**
     * @var Login
     */
    private $subject;

    /**
     * @var LoadUser|MockObject
     */
    private $loadUser;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    /**
     * @var Token|MockObject
     */
    private $token;

    /**
     * @var PasswordHasher|MockObject
     */
    private $passwordHasher;

    public function setUp()
    {
        $this->loadUser       = $this->getMock(LoadUser::class, [], [], '', false);
        $this->dispatcher     = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->token          = $this->getMock(Token::class, [], [], '', false);
        $this->passwordHasher = $this->getMock(PasswordHasher::class, [], [], '', false);

        $this->subject = new Login($this->loadUser, $this->token, $this->passwordHasher);
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    /**
     * @expectedException \BrainExe\Core\Application\UserException
     * @expectedExceptionMessage Invalid Username
     */
    public function testTryLoginWithInvalidUsername()
    {
        $username       = 'user name';
        $password       = 'password';
        $oneTimeToken   = 'token';
        $session        = new Session(new MockArraySessionStorage());

        $this->loadUser
            ->expects($this->once())
            ->method('loadUserByUsername')
            ->with($username)
            ->willReturn(null);

        $this->subject->tryLogin($username, $password, $oneTimeToken, $session);
    }

    /**
     * @expectedException \BrainExe\Core\Application\UserException
     * @expectedExceptionMessage Invalid Password
     */
    public function testTryLoginWithInvalidHash()
    {
        $username       = 'user name';
        $password       = 'password';
        $oneTimeToken   = 'token';
        $session        = new Session(new MockArraySessionStorage());
        $userPassword   = 'real password';

        $userVo = new UserVO();
        $userVo->password_hash = $userPassword;

        $this->loadUser
            ->expects($this->once())
            ->method('loadUserByUsername')
            ->with($username)
            ->willReturn($userVo);

        $this->passwordHasher
            ->expects($this->once())
            ->method('verifyHash')
            ->with($password, $userPassword)
            ->willReturn(false);

        $this->subject->tryLogin(
            $username,
            $password,
            $oneTimeToken,
            $session
        );
    }

    public function testTryLogin()
    {
        $username       = 'user name';
        $password       = 'password';
        $oneTimeToken   = 'token';
        $session        = new Session(new MockArraySessionStorage());
        $userPassword   = 'real password';

        $userVo = new UserVO();
        $userVo->id              = $userId = 42;
        $userVo->password_hash   = $userPassword;
        $userVo->one_time_secret = '';

        $this->loadUser
            ->expects($this->once())
            ->method('loadUserByUsername')
            ->with($username)
            ->willReturn($userVo);

        $this->passwordHasher
            ->expects($this->once())
            ->method('verifyHash')
            ->with($password, $userPassword)
            ->willReturn(true);

        $authenticationVo = new AuthenticationDataVO(
            $userVo,
            $password,
            $oneTimeToken
        );

        $event = new AuthenticateUserEvent(
            $authenticationVo,
            AuthenticateUserEvent::CHECK
        );
        $this->dispatcher
            ->expects($this->at(0))
            ->method('dispatchEvent')
            ->with($event);

        $event = new AuthenticateUserEvent(
            $authenticationVo,
            AuthenticateUserEvent::AUTHENTICATED
        );
        $this->dispatcher
            ->expects($this->at(01))
            ->method('dispatchEvent')
            ->with($event);

        $actualResult = $this->subject->tryLogin(
            $username,
            $password,
            $oneTimeToken,
            $session
        );

        $this->assertEquals($userVo, $actualResult);
        $this->assertEquals($userId, $session->get('user_id'));
        $this->assertEquals($event->getAuthenticationData(), $authenticationVo);
    }

    /**
     * @expectedException \BrainExe\Core\Application\UserException
     * @expectedExceptionMessage Invalid Token
     */
    public function testLoginWithInvalidToken()
    {
        $token = 'token';

        $session = new Session(new MockArraySessionStorage());

        $this->token
            ->expects($this->once())
            ->method('getToken')
            ->willReturn(null);

        $this->subject->loginWithToken($token, $session);
    }

    public function testNeedsToken()
    {
        $username = 'username';
        $user     = new UserVO();
        $user->one_time_secret = 'token';

        $this->loadUser
            ->expects($this->once())
            ->method('loadUserByUsername')
            ->with($username)
            ->willReturn($user);

        $actual = $this->subject->needsOneTimeToken($username);

        $this->assertTrue($actual);
    }

    public function testNeedsTokenWithoutUser()
    {
        $username = 'username';

        $this->loadUser
            ->expects($this->once())
            ->method('loadUserByUsername')
            ->with($username)
            ->willThrowException(new UsernameNotFoundException());

        $actual = $this->subject->needsOneTimeToken($username);

        $this->assertFalse($actual);
    }

    public function testLoginWithToken()
    {
        $token = 'token';

        $userVo = new UserVO();
        $userId = 42;

        $tokenData = [
            'userId' => 42,
            'roles' => [Login::TOKEN_LOGIN]
        ];

        $session = new Session(new MockArraySessionStorage());

        $this->token
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($tokenData);

        $this->loadUser
            ->expects($this->once())
            ->method('loadUserById')
            ->with($userId)
            ->willReturn($userVo);

        $actual = $this->subject->loginWithToken($token, $session);

        $this->assertEquals($userVo, $actual);
    }
}
