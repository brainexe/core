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
        $one_time_token = 'token';
        $session        = new Session(new MockArraySessionStorage());

        $this->mockDatabaseUserProvider
        ->expects($this->once())
        ->method('loadUserByUsername')
        ->with($username)
        ->will($this->returnValue(null));

        $this->subject->tryLogin($username, $password, $one_time_token, $session);
    }

    /**
     * @expectedException \BrainExe\Core\Application\UserException
     * @expectedExceptionMessage Invalid Password
     */
    public function testTryLoginWithInvalidHash()
    {
        $username       = 'user name';
        $password       = 'password';
        $one_time_token = 'token';
        $session        = new Session(new MockArraySessionStorage());
        $user_password = 'real password';

        $user_vo = new UserVO();
        $user_vo->password_hash = $user_password;

        $this->mockDatabaseUserProvider
        ->expects($this->once())
        ->method('loadUserByUsername')
        ->with($username)
        ->will($this->returnValue($user_vo));

        $this->mockDatabaseUserProvider
        ->expects($this->once())
        ->method('verifyHash')
        ->with($password, $user_password)
        ->will($this->returnValue(false));

        $this->subject->tryLogin($username, $password, $one_time_token, $session);
    }

    public function testTryLogin()
    {
        $username       = 'user name';
        $password       = 'password';
        $one_time_token = 'token';
        $session        = new Session(new MockArraySessionStorage());
        $user_password  = 'real password';

        $user_vo = new UserVO();
        $user_vo->id              = $id = 42;
        $user_vo->password_hash   = $user_password;
        $user_vo->one_time_secret = false;

        $this->mockDatabaseUserProvider
        ->expects($this->once())
        ->method('loadUserByUsername')
        ->with($username)
        ->will($this->returnValue($user_vo));

        $this->mockDatabaseUserProvider
        ->expects($this->once())
        ->method('verifyHash')
        ->with($password, $user_password)
        ->will($this->returnValue(true));

        $authentication_vo = new AuthenticationDataVO($user_vo, $password, $one_time_token);

        $event = new AuthenticateUserEvent($authentication_vo, AuthenticateUserEvent::CHECK);
        $this->mockDispatcher
        ->expects($this->at(0))
        ->method('dispatchEvent')
        ->with($event);

        $event = new AuthenticateUserEvent($authentication_vo, AuthenticateUserEvent::AUTHENTICATED);
        $this->mockDispatcher
        ->expects($this->at(01))
        ->method('dispatchEvent')
        ->with($event);

        $actualResult = $this->subject->tryLogin($username, $password, $one_time_token, $session);

        $this->assertEquals($user_vo, $actualResult);
        $this->assertEquals($id, $session->get('user_id'));
        $this->assertEquals($event->getAuthenticationData(), $authentication_vo);
    }
}
