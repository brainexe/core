<?php

namespace Tests\BrainExe\Core\Authentication\Login;

use BrainExe\Core\Authentication\AuthenticationDataVO;
use BrainExe\Core\Authentication\DatabaseUserProvider;
use BrainExe\Core\Authentication\Event\AuthenticateUserEvent;
use BrainExe\Core\Authentication\Login;
use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * @Covers BrainExe\Core\Authentication\Login
 */
class LoginTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Login
     */
    private $subject;

    /**
     * @var DatabaseUserProvider|MockObject
     */
    private $mockDatabaseUserProvider;

    /**
     * @var EventDispatcher|MockObject
     */
    private $mockDispatcher;

    public function setUp()
    {
        $this->mockDatabaseUserProvider = $this->getMock(DatabaseUserProvider::class, [], [], '', false);
        $this->mockDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new Login($this->mockDatabaseUserProvider);
        $this->subject->setEventDispatcher($this->mockDispatcher);
    }

    /**
     * @expectedException \BrainExe\Core\Application\UserException
     * @expectedExceptionMessage Invalid Username
     */
    public function testTryLoginWithInvalidUsername()
    {
        $username       = 'user name';
        $password       = 'password';
        $oneTimeToken = 'token';
        $session        = new Session(new MockArraySessionStorage());

        $this->mockDatabaseUserProvider
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
        $oneTimeToken = 'token';
        $session        = new Session(new MockArraySessionStorage());
        $userPassword = 'real password';

        $userVo = new UserVO();
        $userVo->password_hash = $userPassword;

        $this->mockDatabaseUserProvider
            ->expects($this->once())
            ->method('loadUserByUsername')
            ->with($username)
            ->willReturn($userVo);

        $this->mockDatabaseUserProvider
            ->expects($this->once())
            ->method('verifyHash')
            ->with($password, $userPassword)
            ->willReturn(false);

        $this->subject->tryLogin($username, $password, $oneTimeToken, $session);
    }

    public function testTryLogin()
    {
        $username       = 'user name';
        $password       = 'password';
        $oneTimeToken = 'token';
        $session        = new Session(new MockArraySessionStorage());
        $userPassword  = 'real password';

        $userVo = new UserVO();
        $userVo->id              = $id = 42;
        $userVo->password_hash   = $userPassword;
        $userVo->one_time_secret = false;

        $this->mockDatabaseUserProvider
            ->expects($this->once())
            ->method('loadUserByUsername')
            ->with($username)
            ->willReturn($userVo);

        $this->mockDatabaseUserProvider
            ->expects($this->once())
            ->method('verifyHash')
            ->with($password, $userPassword)
            ->willReturn(true);

        $authenticationVo = new AuthenticationDataVO($userVo, $password, $oneTimeToken);

        $event = new AuthenticateUserEvent($authenticationVo, AuthenticateUserEvent::CHECK);
        $this->mockDispatcher
            ->expects($this->at(0))
            ->method('dispatchEvent')
            ->with($event);

        $event = new AuthenticateUserEvent($authenticationVo, AuthenticateUserEvent::AUTHENTICATED);
        $this->mockDispatcher
            ->expects($this->at(01))
            ->method('dispatchEvent')
            ->with($event);

        $actualResult = $this->subject->tryLogin($username, $password, $oneTimeToken, $session);

        $this->assertEquals($userVo, $actualResult);
        $this->assertEquals($id, $session->get('user_id'));
        $this->assertEquals($event->getAuthenticationData(), $authenticationVo);
    }
}
